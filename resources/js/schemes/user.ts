import { z } from "zod";

const baseUserFields = {
  id: z.number().optional(),
  name: z.string().min(1, 'ユーザ名を入力してください'),
  email: z.email('メールアドレスを入力してください'),
  role: z.number().int().min(1, '権限を選択してください'),
};

// 新規作成用: パスワード必須
export const userCreateFormScheme = z
  .object({
    ...baseUserFields,
    password: z.string().min(8, 'パスワードは8文字以上で入力してください'),
    password_confirmation: z.string().min(1, '確認用パスワードを入力してください'),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'パスワードが一致しません',
    path: ['password_confirmation'],
  });

// 編集用: パスワードは空でもOK、入力する場合のみ8文字以上・一致必須
export const userEditFormScheme = z
  .object({
    ...baseUserFields,
    password: z
      .string()
      .refine((value) => value === '' || value.length >= 8, {
        message: 'パスワードは8文字以上で入力してください',
      }),
    password_confirmation: z.string(),
  })
  .refine(
    (data) => {
      // パスワードが未入力なら確認欄のチェックは不要
      if (data.password === '') {
return true;
}

      return data.password === data.password_confirmation;
    },
    {
      message: 'パスワードが一致しません',
      path: ['password_confirmation'],
    },
  );

export type UserFormInput = z.input<typeof userCreateFormScheme>;
export type UserFormOutput = z.output<typeof userCreateFormScheme>;