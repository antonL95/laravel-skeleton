import { router, useHttp } from '@inertiajs/react';
import { usePasskeyRegister } from '@laravel/passkeys/react';
import { Fingerprint } from 'lucide-react';
import { type FormEventHandler, useState } from 'react';

import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslation } from '@/hooks/use-translation';
import { destroy, registrationOptions, store as passkeyStore } from '@/wayfinder/routes/passkey';
import { store as confirmPassword } from '@/wayfinder/routes/password/confirm';

import PasskeyData = App.Data.PasskeyData;

export default function PasskeysSection({ passkeys, hasPassword }: { passkeys: PasskeyData[]; hasPassword: boolean }) {
    const t = useTranslation();

    const [addOpen, setAddOpen] = useState(false);
    const [addStep, setAddStep] = useState<'confirm' | 'name'>('confirm');
    const [passkeyName, setPasskeyName] = useState('');
    const [deleteTarget, setDeleteTarget] = useState<PasskeyData | null>(null);

    const addConfirmForm = useHttp({ password: '' });
    const deleteConfirmForm = useHttp({ password: '' });
    const deleteForm = useHttp({});

    const {
        register,
        isLoading: registering,
        error: registerError,
        isSupported,
    } = usePasskeyRegister({
        routes: { options: registrationOptions().url, submit: passkeyStore().url },
        onSuccess: () => {
            setAddOpen(false);
            setAddStep('confirm');
            setPasskeyName('');
            addConfirmForm.reset();
            router.reload({ only: ['passkeys'] });
        },
    });

    const openAdd = () => {
        addConfirmForm.reset();
        addConfirmForm.clearErrors();
        setPasskeyName('');
        setAddStep('confirm');
        setAddOpen(true);
    };

    const submitPasswordConfirmation: FormEventHandler = (e) => {
        e.preventDefault();
        addConfirmForm.post(confirmPassword().url, {
            onSuccess: () => setAddStep('name'),
        });
    };

    // Triggered by a fresh click so the WebAuthn ceremony keeps user activation.
    const createPasskey = () => {
        if (passkeyName.trim() === '') {
            return;
        }

        register(passkeyName.trim());
    };

    const closeDelete = () => {
        setDeleteTarget(null);
        deleteConfirmForm.reset();
        deleteConfirmForm.clearErrors();
    };

    const submitDelete = () => {
        const target = deleteTarget;

        if (target === null) {
            return;
        }

        deleteConfirmForm.post(confirmPassword().url, {
            onSuccess: () => {
                deleteForm.delete(destroy(target.uuid).url, {
                    onSuccess: () => {
                        closeDelete();
                        router.reload({ only: ['passkeys'] });
                    },
                });
            },
        });
    };

    if (!hasPassword) {
        return (
            <div className="space-y-6">
                <HeadingSmall title={t('settings.passkeys.title')} description={t('settings.passkeys.description')} />
                <p className="text-sm text-muted-foreground">{t('settings.passkeys.set_password_first')}</p>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <HeadingSmall title={t('settings.passkeys.title')} description={t('settings.passkeys.description')} />

            {passkeys.length > 0 ? (
                <ul className="divide-y divide-border rounded-md border" data-testid="settings-passkeys-list">
                    {passkeys.map((passkey) => (
                        <li key={passkey.uuid} className="flex items-center justify-between gap-4 p-4">
                            <div className="flex min-w-0 items-center gap-3">
                                <Fingerprint className="h-5 w-5 shrink-0 text-muted-foreground" />
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-medium">{passkey.name}</p>
                                    <p className="text-xs text-muted-foreground">
                                        {passkey.authenticator ? `${passkey.authenticator} · ` : ''}
                                        {passkey.lastUsedAt ? t('settings.passkeys.last_used') : t('settings.passkeys.never_used')}
                                    </p>
                                </div>
                            </div>
                            <Button
                                variant="destructive"
                                size="sm"
                                onClick={() => setDeleteTarget(passkey)}
                                data-testid={`settings-passkey-delete-${passkey.uuid}`}
                            >
                                {t('settings.passkeys.delete_button')}
                            </Button>
                        </li>
                    ))}
                </ul>
            ) : (
                <p className="text-sm text-muted-foreground">{t('settings.passkeys.empty')}</p>
            )}

            <div className="space-y-2">
                <Button onClick={openAdd} disabled={!isSupported} data-testid="settings-passkey-add">
                    <Fingerprint className="h-4 w-4" />
                    {t('settings.passkeys.add_button')}
                </Button>
                {!isSupported && <p className="text-sm text-muted-foreground">{t('settings.passkeys.not_supported')}</p>}
            </div>

            <Dialog
                open={addOpen}
                onOpenChange={(open) => {
                    setAddOpen(open);
                    if (!open) {
                        setAddStep('confirm');
                    }
                }}
            >
                <DialogContent data-testid="settings-passkey-add-dialog">
                    {addStep === 'confirm' ? (
                        <form onSubmit={submitPasswordConfirmation} className="space-y-6">
                            <DialogHeader>
                                <DialogTitle>{t('settings.passkeys.confirm_title')}</DialogTitle>
                                <DialogDescription>{t('settings.passkeys.confirm_description')}</DialogDescription>
                            </DialogHeader>

                            <div className="grid gap-2">
                                <Label htmlFor="passkey-confirm-password">{t('settings.passkeys.password_label')}</Label>
                                <Input
                                    id="passkey-confirm-password"
                                    type="password"
                                    autoComplete="current-password"
                                    autoFocus
                                    value={addConfirmForm.data.password}
                                    onChange={(e) => addConfirmForm.setData('password', e.target.value)}
                                />
                                <InputError message={addConfirmForm.errors.password} />
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setAddOpen(false)}>
                                    {t('settings.passkeys.cancel_button')}
                                </Button>
                                <Button type="submit" disabled={addConfirmForm.processing}>
                                    {t('settings.passkeys.continue_button')}
                                </Button>
                            </DialogFooter>
                        </form>
                    ) : (
                        <div className="space-y-6">
                            <DialogHeader>
                                <DialogTitle>{t('settings.passkeys.name_title')}</DialogTitle>
                                <DialogDescription>{t('settings.passkeys.name_description')}</DialogDescription>
                            </DialogHeader>

                            <div className="grid gap-2">
                                <Label htmlFor="passkey-name">{t('settings.passkeys.name_label')}</Label>
                                <Input
                                    id="passkey-name"
                                    type="text"
                                    autoFocus
                                    value={passkeyName}
                                    onChange={(e) => setPasskeyName(e.target.value)}
                                    placeholder={t('settings.passkeys.name_placeholder')}
                                />
                                {registerError && <InputError message={registerError} />}
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setAddOpen(false)}>
                                    {t('settings.passkeys.cancel_button')}
                                </Button>
                                <Button
                                    type="button"
                                    onClick={createPasskey}
                                    disabled={registering || passkeyName.trim() === ''}
                                    data-testid="settings-passkey-create"
                                >
                                    {t('settings.passkeys.create_button')}
                                </Button>
                            </DialogFooter>
                        </div>
                    )}
                </DialogContent>
            </Dialog>

            <Dialog
                open={deleteTarget !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        closeDelete();
                    }
                }}
            >
                <DialogContent data-testid="settings-passkey-delete-dialog">
                    <DialogHeader>
                        <DialogTitle>{t('settings.passkeys.delete_title')}</DialogTitle>
                        <DialogDescription>{t('settings.passkeys.delete_description')}</DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-2">
                        <Label htmlFor="passkey-delete-password">{t('settings.passkeys.password_label')}</Label>
                        <Input
                            id="passkey-delete-password"
                            type="password"
                            autoComplete="current-password"
                            value={deleteConfirmForm.data.password}
                            onChange={(e) => deleteConfirmForm.setData('password', e.target.value)}
                        />
                        <InputError message={deleteConfirmForm.errors.password} />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={closeDelete}>
                            {t('settings.passkeys.cancel_button')}
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={submitDelete}
                            disabled={deleteConfirmForm.processing || deleteForm.processing}
                            data-testid="settings-passkey-delete-confirm"
                        >
                            {t('settings.passkeys.delete_button')}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
