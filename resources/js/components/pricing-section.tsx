import { Button } from '@/components/ui/button';
import { useTranslation } from '@/hooks/use-translation';
import type { SharedData } from '@/types';
import { dashboard } from '@/wayfinder/routes';
import { store } from '@/wayfinder/routes/checkout';
import { Link, usePage } from '@inertiajs/react';
import { clsx } from 'clsx';
import { CheckIcon } from 'lucide-react';
import StripeProductData = App.Data.StripeProductData;

export default function PricingSection({ pricing }: { pricing?: StripeProductData[] }) {
    const t = useTranslation();
    const { auth } = usePage<SharedData>().props;

    if (!pricing) {
        return <></>;
    }

    const tiers = [];

    for (const product of pricing) {
        tiers.push({
            name: product.name,
            id: product.id,
            price: product.price,
            description: product.description,
            features: product.features,
            featured: product.featured,
            free: false,
            subscription: product.recurring,
            cta: !auth.user ? t('register') : !product.recurring ? t('pricing.credit_cta') : t('pricing.subscribe_cta'),
            priceId: product.priceId,
        });
    }

    return (
        <form className="group/tiers">
            <div className="mx-auto max-w-7xl px-6 lg:px-8">
                <div className="mx-auto max-w-4xl text-center">
                    <h2 className="mt-2 text-5xl font-semibold tracking-tight text-balance text-foreground sm:text-4xl">{t('pricing.heading')}</h2>
                </div>
                <p className="mx-auto mt-6 max-w-xl text-center text-lg font-medium text-pretty text-muted-foreground sm:text-lg/6">
                    {t('pricing.description')}
                </p>
                <div className="isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-8 md:max-w-2xl md:grid-cols-2 lg:max-w-4xl xl:mx-0 xl:max-w-none xl:grid-cols-4">
                    {tiers.map((tier) => (
                        <div
                            key={tier.id}
                            data-featured={tier.featured ? 'true' : undefined}
                            data-free={tier.free ? 'true' : undefined}
                            data-subscription={tier.subscription ? 'true' : undefined}
                            className="group/tier rounded-3xl bg-background/35 p-8 ring-1 ring-foreground data-featured:ring-2 data-featured:ring-primary"
                        >
                            <div className="flex items-center justify-between gap-x-4">
                                <h3 id={`tier-${tier.id}`} className="text-lg/8 font-semibold text-foreground group-data-featured/tier:text-primary">
                                    {tier.name}
                                </h3>
                                <p className="rounded-full bg-primary px-2.5 py-1 text-xs/5 font-semibold text-foreground group-not-data-featured/tier:hidden">
                                    {t('pricing.most_popular')}
                                </p>
                            </div>
                            <p className="mt-4 text-sm/6 text-muted-foreground">{tier.description}</p>
                            <p className="mt-6 flex items-baseline gap-x-1">
                                <span className="text-2xl font-semibold tracking-tight text-foreground">{tier.price}</span>
                                <span className="text-sm/6 font-semibold text-muted-foreground group-not-data-subscription/tier:hidden group-data-free/tier:hidden">
                                    /{t('pricing.monthly')}
                                </span>
                            </p>
                            <Button
                                variant={tier.featured ? 'default' : 'outline'}
                                value={tier.id}
                                name="tier"
                                type="submit"
                                data-tier={tier.id}
                                aria-describedby={`tier-${tier.id}`}
                                className={clsx('mt-6 w-full', !tier.featured ? 'bg-transparent hover:bg-primary' : '')}
                                asChild
                            >
                                {tier.free ? (
                                    <Link href={dashboard().url} prefetch>
                                        {tier.cta}
                                    </Link>
                                ) : (
                                    <Link
                                        href={
                                            store({
                                                query: {
                                                    price_id: tier.priceId,
                                                },
                                            }).url
                                        }
                                    >
                                        {tier.cta}
                                    </Link>
                                )}
                            </Button>
                            <ul role="list" className="mt-8 space-y-3 text-sm/6 text-foreground">
                                {tier.features.map((feature) => (
                                    <li key={feature} className="flex gap-x-3">
                                        <CheckIcon aria-hidden="true" className="h-6 w-5 flex-none text-primary" />
                                        {feature}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>
            </div>
        </form>
    );
}
