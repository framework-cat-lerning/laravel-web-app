import { usePage } from '@inertiajs/react';
import type { Auth } from '@/types/auth';

/**
 * Inertia の共有プロパティから認証情報を取得するフック
 */
export function useAuth(): { auth: Auth } {
    const { auth } = usePage().props;

    return { auth };
}
