import { Toaster } from '@/components/ui/sonner';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEcho } from '@laravel/echo-react';
import { CheckCircle, OctagonAlert } from 'lucide-react';
import { useEffect } from 'react';
import { toast } from 'sonner';
import FlashData = App.Data.FlashData;

export default function Toast({ flash }: { flash?: FlashData }) {
    const { auth } = usePage<SharedData>().props;

    useEcho<FlashData>('App.Models.User.' + auth.user?.id, 'FlashMessageEvent', function (e) {
        toast(e.title, {
            description: e.description,
            icon: e.type === 'success' ? <CheckCircle /> : <OctagonAlert />,
            duration: 5000,
            closeButton: true,
            dismissible: true,
        });
    });

    useEffect(() => {
        if (!flash) return;

        toast(flash.title, {
            description: flash.description,
            icon: flash.type === 'success' ? <CheckCircle /> : <OctagonAlert />,
            duration: 5000,
            closeButton: true,
            dismissible: true,
        });
    }, [flash]);

    return <Toaster />;
}
