import { Form, Head } from '@inertiajs/react';
import { loggedIn } from '@/routes';

function Login() {
    return (
        <>
            <Head title="ログイン" />
            <div className="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:p-8 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
                <main className="w-full max-w-[400px]">
                    <div className="rounded-lg bg-white p-8 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                        <div className="mb-6">
                            <h1 className="text-xl font-medium">ログイン</h1>
                            <p className="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                メールアドレスとパスワードを入力してください
                            </p>
                        </div>

                        <Form
                            {...loggedIn.post()}
                            resetOnSuccess={['password']}
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="flex flex-col gap-1.5">
                                        <label
                                            htmlFor="email"
                                            className="text-sm font-medium"
                                        >
                                            メールアドレス
                                        </label>
                                        <input
                                            id="email"
                                            name="email"
                                            type="email"
                                            required
                                            autoFocus
                                            autoComplete="email"
                                            className="rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm outline-none focus:border-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC]"
                                        />
                                        {errors.email && (
                                            <p className="text-sm text-[#f53003] dark:text-[#FF4433]">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <label
                                            htmlFor="password"
                                            className="text-sm font-medium"
                                        >
                                            パスワード
                                        </label>
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            required
                                            autoComplete="current-password"
                                            className="rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm outline-none focus:border-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC]"
                                        />
                                        {errors.password && (
                                            <p className="text-sm text-[#f53003] dark:text-[#FF4433]">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>

                                    <label className="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        <input
                                            name="remember"
                                            type="checkbox"
                                            className="rounded border-[#e3e3e0] dark:border-[#3E3E3A]"
                                        />
                                        ログイン状態を保持する
                                    </label>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="mt-2 inline-flex items-center justify-center rounded-sm border border-black bg-[#1b1b18] px-5 py-2 text-sm leading-normal text-white transition-colors hover:bg-black disabled:cursor-not-allowed disabled:opacity-60 dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                                    >
                                        {processing ? 'ログイン中...' : 'ログイン'}
                                    </button>
                                </>
                            )}
                        </Form>
                    </div>
                </main>
            </div>
        </>
    );
}

// ゲストページは共通レイアウト（MainLayout）を適用しない
Login.layout = null;

export default Login;
