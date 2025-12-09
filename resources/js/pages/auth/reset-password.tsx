import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslation } from '@/hooks/use-translation';
import AuthLayout from '@/layouts/auth-layout';
import NewPasswordController from '@/wayfinder/actions/Laravel/Fortify/Http/Controllers/NewPasswordController';

interface ResetPasswordProps {
    token: string;
    email: string;
}

export default function ResetPassword({ token, email }: ResetPasswordProps) {
    const t = useTranslation();

    return (
        <AuthLayout title={t('auth.reset_password.title')} description={t('auth.reset_password.description')}>
            <Head title={t('auth.reset_password.page_title')} />

            <Form
                {...NewPasswordController.store.form()}
                transform={(data) => ({ ...data, token, email })}
                resetOnSuccess={['password', 'password_confirmation']}
            >
                {({ processing, errors }) => (
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="email">{t('auth.reset_password.email_label')}</Label>
                            <Input id="email" type="email" name="email" autoComplete="email" value={email} className="mt-1 block w-full" readOnly />
                            <InputError message={errors.email} className="mt-2" />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password">{t('auth.reset_password.password_label')}</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                autoComplete="new-password"
                                className="mt-1 block w-full"
                                autoFocus
                                placeholder={t('auth.reset_password.password_placeholder')}
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password_confirmation">{t('auth.reset_password.password_confirmation_label')}</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                autoComplete="new-password"
                                className="mt-1 block w-full"
                                placeholder={t('auth.reset_password.password_confirmation_placeholder')}
                            />
                            <InputError message={errors.password_confirmation} className="mt-2" />
                        </div>

                        <Button type="submit" className="mt-4 w-full" disabled={processing}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            {t('auth.reset_password.submit')}
                        </Button>
                    </div>
                )}
            </Form>
        </AuthLayout>
    );
}
