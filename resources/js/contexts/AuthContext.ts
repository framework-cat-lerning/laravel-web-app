import { usePage } from '@inertiajs/react';
import type { Auth } from '@/types/auth';

/**
 * Inertia がサーバーから配布する共有プロパティ auth を返すフック
 *
 * 認証情報は HandleInertiaRequests::share() で全ページに渡されるため
 * クライアント側での再取得は不要
 */
export function useAuth(): { auth: Auth } {
    const { auth } = usePage().props;

    return { auth };
}
