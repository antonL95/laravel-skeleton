import PricingSection from '@/components/pricing-section';
import GuestLayout from '@/layouts/guest-layout';
import StripeProductData = App.Data.StripeProductData;

export default function Index({ pricing }: { pricing?: StripeProductData[] }) {
    return (
        <GuestLayout>
            <PricingSection pricing={pricing} />
        </GuestLayout>
    );
}
