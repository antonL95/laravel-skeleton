import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import FlashData = App.Data.FlashData;

export default function useToast() {
    const page = usePage<SharedData>();
    return page.props.flash as unknown as FlashData;
}
