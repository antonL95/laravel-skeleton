import { type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

/**
 * A hook to access translations from the page props.
 * Supports dot notation for accessing nested translations (e.g., 'auth.login', 'feature.cta').
 * Can also return arrays for iteration (e.g., 'cta.features').
 * Supports named parameters that replace placeholders in the text (e.g., 'Máte zakoupených :credits kreditů').
 *
 * @example
 * // Basic usage for string translations
 * const t = useTranslation();
 * return <h1>{t('dashboard.heading')}</h1>;
 *
 * @example
 * // Usage with arrays for iteration
 * const t = useTranslation();
 * return (
 *   <div>
 *     {t<Array<{ icon: string; text: string }>>('cta.features').map((feature, index) => (
 *       <div key={index}>
 *         <div dangerouslySetInnerHTML={{ __html: feature.icon }} />
 *         <div>{feature.text}</div>
 *       </div>
 *     ))}
 *   </div>
 * );
 *
 * @example
 * // Usage with named parameters
 * const t = useTranslation();
 * return <p>{t('profile_section.credits.content', { credits: 10 })}</p>;
 * // Renders: "Máte zakoupených 10 kreditů"
 *
 * @returns A function that takes a key and optional parameters and returns the corresponding translation.
 */
export function useTranslation() {
    const { translations } = usePage<SharedData>().props;

    /**
     * Get a translation by key.
     * Supports dot notation for accessing nested translations.
     * Can return arrays for iteration.
     * Supports named parameters that replace placeholders in the text.
     *
     * @param key The key to look up in the translations.messages object (e.g., 'auth.login', 'cta.features').
     * @param params Optional object containing named parameters to replace placeholders in the translation.
     * @returns The translation value, which can be a string, array, or the original key if not found.
     */
    const t = <T = string,>(key: string, params?: Record<string, string | number>): T => {
        if (!translations || !translations.messages) {
            return key as unknown as T;
        }

        // Handle flat keys (backward compatibility)
        if (translations.messages[key] !== undefined) {
            let value = translations.messages[key];

            // Replace placeholders with parameters if it's a string and params are provided
            if (typeof value === 'string' && params) {
                value = replacePlaceholders(value, params);
            }

            return value as unknown as T;
        }

        // Handle nested keys with dot notation
        const parts = key.split('.');
        let value: unknown = translations.messages;

        for (const part of parts) {
            if (value === undefined || value === null || typeof value !== 'object') {
                return key as unknown as T; // Return the original key if the path is invalid
            }
            value = (value as Record<string, unknown>)[part];
        }

        // Replace placeholders with parameters if it's a string and params are provided
        if (typeof value === 'string' && params) {
            value = replacePlaceholders(value, params);
        }

        // Return the value if it exists, otherwise return the key
        return value !== undefined ? (value as T) : (key as unknown as T);
    };

    /**
     * Replace placeholders in a string with values from the params object.
     * Placeholders are in the format ":paramName".
     *
     * @param text The text containing placeholders.
     * @param params The object containing parameter values.
     * @returns The text with placeholders replaced by parameter values.
     */
    const replacePlaceholders = (text: string, params: Record<string, string | number>): string => {
        return Object.entries(params).reduce((result, [key, value]) => {
            return result.replace(new RegExp(`:${key}`, 'g'), String(value));
        }, text);
    };

    return t;
}
