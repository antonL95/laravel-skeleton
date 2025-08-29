declare namespace App.Data {
export type FlashData = {
type: App.Enums.FlashMessageType;
title: string;
description: string | null;
};
export type StripeProductData = {
id: string;
name: string;
description: string;
priceId: string;
priceAmount: number;
currency: string;
price: string;
features: Array<string>;
recurring: boolean;
featured: boolean;
};
export type UserData = {
id: number;
name: string;
email: string;
isSubscribed: boolean;
initials: string;
emailVerifiedAt: string | null;
};
}
declare namespace App.Enums {
export type FlashMessageAction = 'delete' | 'update' | 'create' | 'renew';
export type FlashMessageType = 'success' | 'danger';
}
