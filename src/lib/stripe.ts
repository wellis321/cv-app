import { loadStripe } from '@stripe/stripe-js';
import Stripe from 'stripe';

// Client-side Stripe instance
export const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY);

// Server-side Stripe instance - only create if environment variable is available
export const stripe = import.meta.env.STRIPE_SECRET_KEY
    ? new Stripe(import.meta.env.STRIPE_SECRET_KEY, {
        apiVersion: '2025-07-30.basil'
    })
    : null;

// Full access product configuration
export const FULL_ACCESS_PRICE_ID = import.meta.env.VITE_STRIPE_PRICE_ID;
export const FULL_ACCESS_AMOUNT = 999; // £9.99 in pence (Stripe uses pence for GBP)

// Helper function to check if Stripe is properly configured
export function isStripeConfigured(): boolean {
    return !!(
        import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY &&
        import.meta.env.STRIPE_SECRET_KEY &&
        import.meta.env.STRIPE_WEBHOOK_SECRET
    );
}

// Types for payment data
export interface PaymentIntentData {
    amount: number;
    currency: string;
    metadata?: Record<string, string>;
}

export interface CreatePaymentIntentResponse {
    clientSecret: string;
    paymentIntentId: string;
}
