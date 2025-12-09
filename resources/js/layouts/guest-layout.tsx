import AppLogoIcon from '@/components/app-logo-icon';
import { useTranslation } from '@/hooks/use-translation';
import type { SharedData } from '@/types';
import { dashboard, home, login, register } from '@/wayfinder/routes';
import { Link, usePage } from '@inertiajs/react';
import { ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
}

export default function ({ children }: AppLayoutProps) {
    const { auth } = usePage<SharedData>().props;
    const t = useTranslation();

    return (
        <div className="flex min-h-screen flex-col items-center bg-background p-6 text-foreground lg:justify-center lg:p-8">
            <header className="z-10 mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
                <nav className="flex items-center justify-between gap-4">
                    <Link href={home().url} prefetch>
                        <AppLogoIcon className="h-10 fill-current text-white" />
                    </Link>
                    <div>
                        {auth?.user ? (
                            <Link
                                prefetch
                                href={dashboard().url}
                                className="inline-block rounded-sm border border-muted px-5 py-1.5 text-sm leading-normal text-foreground hover:border-foreground"
                            >
                                {t('navigation.dashboard')}
                            </Link>
                        ) : (
                            <>
                                <Link
                                    prefetch
                                    href={login().url}
                                    className="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-foreground hover:border-foreground"
                                >
                                    {t('log_in')}
                                </Link>
                                <Link
                                    prefetch
                                    href={register().url}
                                    className="inline-block rounded-sm border border-muted px-5 py-1.5 text-sm leading-normal text-foreground hover:border-foreground"
                                >
                                    {t('register')}
                                </Link>
                            </>
                        )}
                    </div>
                </nav>
            </header>

            <main
                className={`z-10 flex w-full flex-col items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0`}
            >
                {children}
            </main>
            <footer className={`z-10 mt-10 flex w-full flex-col space-y-2 lg:mt-20`}></footer>
        </div>
    );
}
