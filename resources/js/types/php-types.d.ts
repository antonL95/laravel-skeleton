declare namespace App {
namespace Data {
export type FlashData = {
readonly type: App.Enums.FlashMessageType,
readonly title: string,
readonly description: string | null,
};
export type PasskeyData = {
readonly uuid: string,
readonly name: string,
readonly authenticator: string | null,
readonly lastUsedAt: string | null,
readonly createdAt: string | null,
};
export type StripeProductData = {
readonly id: string,
readonly name: string,
readonly description: string,
readonly priceId: string,
readonly priceAmount: number,
readonly currency: string,
readonly price: string,
readonly features: string[],
readonly recurring: boolean,
readonly featured: boolean,
};
export type UserData = {
readonly id: number,
readonly name: string,
readonly email: string,
readonly isSubscribed: boolean,
readonly initials: string,
readonly emailVerifiedAt: string | null,
};
}
namespace Enums {
export type CheckoutStatus = 'paid' | 'unpaid' | 'no_payment_required';
export type FlashMessageAction = 'delete' | 'update' | 'create';
export type FlashMessageType = 'success' | 'danger';
}
}
