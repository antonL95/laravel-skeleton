import HeadingSmall from '@/components/heading-small';
import TwoFactorRecoveryCodes from '@/components/two-factor-recovery-codes';
import TwoFactorSetupModal from '@/components/two-factor-setup-modal';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useTranslation } from '@/hooks/use-translation';
import { useTwoFactorAuth } from '@/hooks/use-two-factor-auth';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { type BreadcrumbItem } from '@/types';
import { disable, enable, show } from '@/wayfinder/routes/two-factor';
import { Form, Head } from '@inertiajs/react';
import { ShieldBan, ShieldCheck } from 'lucide-react';
import { useState } from 'react';

interface TwoFactorProps {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'settings.two_factor.page_title',
        href: show.url(),
    },
];

export default function TwoFactor({ requiresConfirmation = false, twoFactorEnabled = false }: TwoFactorProps) {
    const { qrCodeSvg, hasSetupData, manualSetupKey, clearSetupData, fetchSetupData, recoveryCodesList, fetchRecoveryCodes, errors } =
        useTwoFactorAuth();
    const [showSetupModal, setShowSetupModal] = useState<boolean>(false);
    const t = useTranslation();

    return (
        <AppLayout breadcrumbs={breadcrumbs.map((b) => ({ ...b, title: t(b.title) }))}>
            <Head title={t('settings.two_factor.page_title')} />
            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title={t('settings.two_factor.heading')} description={t('settings.two_factor.description')} />
                    {twoFactorEnabled ? (
                        <div className="flex flex-col items-start justify-start space-y-4">
                            <Badge variant="default">{t('settings.two_factor.enabled_badge')}</Badge>
                            <p className="text-muted-foreground">{t('settings.two_factor.enabled_paragraph')}</p>

                            <TwoFactorRecoveryCodes recoveryCodesList={recoveryCodesList} fetchRecoveryCodes={fetchRecoveryCodes} errors={errors} />

                            <div className="relative inline">
                                <Form {...disable.form()}>
                                    {({ processing }) => (
                                        <Button variant="destructive" type="submit" disabled={processing}>
                                            <ShieldBan /> {t('settings.two_factor.disable_button')}
                                        </Button>
                                    )}
                                </Form>
                            </div>
                        </div>
                    ) : (
                        <div className="flex flex-col items-start justify-start space-y-4">
                            <Badge variant="destructive">{t('settings.two_factor.disabled_badge')}</Badge>
                            <p className="text-muted-foreground">{t('settings.two_factor.disabled_paragraph')}</p>

                            <div>
                                {hasSetupData ? (
                                    <Button onClick={() => setShowSetupModal(true)}>
                                        <ShieldCheck />
                                        {t('settings.two_factor.continue_setup')}
                                    </Button>
                                ) : (
                                    <Form {...enable.form()} onSuccess={() => setShowSetupModal(true)}>
                                        {({ processing }) => (
                                            <Button type="submit" disabled={processing}>
                                                <ShieldCheck />
                                                {t('settings.two_factor.enable_button')}
                                            </Button>
                                        )}
                                    </Form>
                                )}
                            </div>
                        </div>
                    )}

                    <TwoFactorSetupModal
                        isOpen={showSetupModal}
                        onClose={() => setShowSetupModal(false)}
                        requiresConfirmation={requiresConfirmation}
                        twoFactorEnabled={twoFactorEnabled}
                        qrCodeSvg={qrCodeSvg}
                        manualSetupKey={manualSetupKey}
                        clearSetupData={clearSetupData}
                        fetchSetupData={fetchSetupData}
                        errors={errors}
                    />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
