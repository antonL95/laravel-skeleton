import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslation } from '@/hooks/use-translation';
import AuthLayout from '@/layouts/auth-layout';
import ConfirmablePasswordController from '@/wayfinder/actions/Laravel/Fortify/Http/Controllers/ConfirmablePasswordController';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

export default function ConfirmPassword() {
    const t = useTranslation();

    return (
        <AuthLayout title={t('auth.confirm_password.title')} description={t('auth.confirm_password.description')}>
            <Head title={t('auth.confirm_password.page_title')} />

            <Form {...ConfirmablePasswordController.store.form()} resetOnSuccess={['password']}>
                {({ processing, errors }) => (
                    <div className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="password">{t('auth.confirm_password.password_label')}</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                placeholder={t('auth.confirm_password.password_placeholder')}
                                autoComplete="current-password"
                                autoFocus
                            />

                            <InputError message={errors.password} />
                        </div>

                        <div className="flex items-center">
                            <Button className="w-full" disabled={processing}>
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                {t('auth.confirm_password.submit')}
                            </Button>
                        </div>
                    </div>
                )}
            </Form>
        </AuthLayout>
    );
}
