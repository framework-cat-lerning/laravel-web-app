import { z } from "zod";

export const userFormScheme = z.object({
    id: z.number().optional(),
    name: z.string().min(1, 'ユーザ名を入力してください'),
    email: z.email('メールアドレスを入力してください'),
    password: z.string().min(8, 'パスワードは8文字以上で入力してください'),
    role: z.number().int().min(1, '権限を選択してください'),
});

export type UserFormInput = z.input<typeof userFormScheme>;
export type UserFormOutput = z.output<typeof userFormScheme>;
