import { Button } from '@/components/ui/button';
import { useTranslation } from '@/hooks/use-translation';
import GuestLayout from '@/layouts/guest-layout';
import { dashboard } from '@/wayfinder/routes';
import { Head, Link } from '@inertiajs/react';

export default function Error({ status }: { status: number }) {
    const title = {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[status];

    const description = {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[status];

    const t = useTranslation();

    return (
        <GuestLayout>
            <Head title={title} />
            <div className="text-center">
                <p className="text-base font-semibold text-primary">{status}</p>
                <h1 className="mt-4 text-5xl font-semibold tracking-tight text-balance text-foreground sm:text-7xl">{title}</h1>
                <p className="mt-6 text-lg font-medium text-pretty text-muted-foreground sm:text-xl/8">{description}</p>
                <div className="mt-10 flex items-center justify-center gap-x-6">
                    <Button asChild className="bg-primary px-8 py-6 text-lg text-primary-foreground hover:bg-primary/90">
                        <Link href={dashboard()}>{t('navigation.home')}</Link>
                    </Button>
                </div>
            </div>
        </GuestLayout>
    );
}
