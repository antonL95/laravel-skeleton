import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import { useTranslation } from '@/hooks/use-translation';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { appearance } from '@/wayfinder/routes';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'settings.appearance.page_title',
        href: appearance().url,
    },
];

export default function Appearance() {
    const t = useTranslation();

    return (
        <AppLayout breadcrumbs={breadcrumbs.map((b) => ({ ...b, title: t(b.title) }))}>
            <Head title={t('settings.appearance.page_title')} />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title={t('settings.appearance.heading')} description={t('settings.appearance.description')} />
                    <AppearanceTabs />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
