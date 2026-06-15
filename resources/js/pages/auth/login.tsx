import { Form, Head } from '@inertiajs/react';
import { usePasskeyVerify } from '@laravel/passkeys/react';
import { Fingerprint, LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslation } from '@/hooks/use-translation';
import AuthLayout from '@/layouts/auth-layout';
import AuthenticatedSessionController from '@/wayfinder/Laravel/Fortify/Http/Controllers/AuthenticatedSessionController';
import { dashboard, register } from '@/wayfinder/routes';
import { login as passkeyLogin, loginOptions } from '@/wayfinder/routes/passkey';
import { request } from '@/wayfinder/routes/password';

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const t = useTranslation();

    const {
        verify,
        isLoading: verifying,
        error: passkeyError,
        isSupported: passkeysSupported,
    } = usePasskeyVerify({
        routes: { options: loginOptions().url, submit: passkeyLogin().url },
        onSuccess: (response) => {
            window.location.href = response.redirect && response.redirect !== '/' ? response.redirect : dashboard().url;
        },
    });

    return (
        <AuthLayout title={t('auth.login.title')} description={t('auth.login.description')}>
            <Head title={t('auth.login.page_title')} />

            <Form {...AuthenticatedSessionController.store.form()} resetOnSuccess={['password']} className="flex flex-col gap-6">
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="email">{t('auth.login.email_label')}</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="email"
                                    placeholder={t('auth.login.email_placeholder')}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <div className="flex items-center">
                                    <Label htmlFor="password">{t('auth.login.password_label')}</Label>
                                    {canResetPassword && (
                                        <TextLink href={request()} className="ml-auto text-sm" tabIndex={5}>
                                            {t('auth.login.forgot_password')}
                                        </TextLink>
                                    )}
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    tabIndex={2}
                                    autoComplete="current-password"
                                    placeholder={t('auth.login.password_placeholder')}
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex items-center space-x-3">
                                <Checkbox id="remember" name="remember" tabIndex={3} />
                                <Label htmlFor="remember">{t('auth.login.remember_me')}</Label>
                            </div>

                            <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                {t('auth.login.submit')}
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            {t('auth.login.no_account')}{' '}
                            <TextLink href={register()} tabIndex={5}>
                                {t('auth.login.sign_up')}
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>

            {passkeysSupported && (
                <div className="flex flex-col gap-4">
                    <div className="relative text-center text-sm text-muted-foreground after:absolute after:inset-x-0 after:top-1/2 after:-z-10 after:h-px after:bg-border">
                        <span className="bg-background px-2">{t('auth.login.or')}</span>
                    </div>
                    <Button type="button" variant="outline" className="w-full" onClick={verify} disabled={verifying} data-testid="login-passkey">
                        {verifying ? <LoaderCircle className="h-4 w-4 animate-spin" /> : <Fingerprint className="h-4 w-4" />}
                        {t('auth.login.passkey_button')}
                    </Button>
                    {passkeyError && <InputError message={passkeyError} />}
                </div>
            )}

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
        </AuthLayout>
    );
}
