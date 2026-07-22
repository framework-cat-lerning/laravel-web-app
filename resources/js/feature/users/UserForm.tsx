import { zodResolver } from '@hookform/resolvers/zod';
import { router, usePage } from '@inertiajs/react';
import Alert from '@mui/material/Alert';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import MenuItem from '@mui/material/MenuItem';
import Select from '@mui/material/Select';
import TextField from '@mui/material/TextField';
import { useEffect, useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import type { UseFormSetError } from 'react-hook-form';
import InputRow from '@/components/form/InputRow';
import { index as userList, store as userStore, update as userUpdate } from '@/routes/admin/users';
import { userCreateFormScheme, userEditFormScheme } from '@/schemes/user';
import type { UserFormOutput } from '@/schemes/user';
import type { UserFormInput } from '@/schemes/user';
import type { UserRole } from '@/types/cases';

interface UserFormProps {
  form_type: 'new' | 'edit';
  user: UserFormInput;
  options: {
    roles: UserRole[];
  };
}

const FIELD_NAMES = ['name', 'email', 'password', 'password_confirmation', 'role'] as const;

function applyServerErrors(
  serverErrors: Record<string, string>,
  setError: UseFormSetError<UserFormInput>,
  setSubmitError: (message: string) => void,
): void {
  let hasFieldError = false;

  FIELD_NAMES.forEach((field) => {
    if (serverErrors[field]) {
      hasFieldError = true;
      setError(field, { type: 'server', message: serverErrors[field] });
    }
  });

  if (!hasFieldError) {
    const firstError = Object.values(serverErrors)[0];
    setSubmitError(firstError || '保存に失敗しました 時間をおいて再度お試しください');
  }
}

export default function UserForm({ form_type, user, options }: UserFormProps) {
  const formScheme = form_type === 'new' ? userCreateFormScheme : userEditFormScheme;
  const { errors: pageErrors } = usePage().props;
  const {
    control,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<UserFormInput, unknown, UserFormOutput>({
    resolver: zodResolver(formScheme),
    defaultValues: {
      name: user?.name ?? '',
      email: user?.email ?? '',
      password: user?.password ?? '',
      password_confirmation: '',
      role: user?.role ?? 0,
    },
  });

  const [submitError, setSubmitError] = useState<string | null>(null);

  // バリデーション失敗でページが戻ってきた場合にもサーバーエラーを反映する
  useEffect(() => {
    if (!pageErrors || typeof pageErrors !== 'object') {
      return;
    }

    const serverErrors = Object.fromEntries(
      Object.entries(pageErrors as Record<string, unknown>)
        .filter(([, message]) => typeof message === 'string')
        .map(([field, message]) => [field, message as string]),
    );

    if (Object.keys(serverErrors).length === 0) {
      return;
    }

    applyServerErrors(serverErrors, setError, setSubmitError);
  }, [pageErrors, setError]);

  const onSubmit = handleSubmit(async (data: UserFormOutput) => {
    setSubmitError(null);

    await new Promise<void>((resolve) => {
      const options = {
        preserveScroll: true,
        // バリデーションエラー時にフォーム入力・エラー表示を維持する
        preserveState: true,
        onError: (serverErrors: Record<string, string>) => {
          applyServerErrors(serverErrors, setError, setSubmitError);
        },
        onHttpException: () => {
          setSubmitError('保存に失敗しました 時間をおいて再度お試しください');
        },
        onNetworkError: () => {
          setSubmitError('通信エラーが発生しました 時間をおいて再度お試しください');
        },
        onFinish: () => resolve(),
      };

      if (form_type === 'new') {
        router.post(userStore.url(), data, options);
      } else {
        router.put(userUpdate.url({ user: user?.id ?? 0 }), data, options);
      }
    });
  });

  return (
    <Box
      sx={{
        py: 2,
        width: '100%',
        maxWidth: '900px',
      }}
    >
      <Box component="form" noValidate onSubmit={onSubmit}>
        {submitError && (
          <Alert severity="error" sx={{ marginBottom: 2 }} onClose={() => setSubmitError(null)}>
            {submitError}
          </Alert>
        )}

        <InputRow label="ユーザ名">
          <Controller
            control={control}
            name="name"
            render={({ field }) => (
              <TextField
                {...field}
                value={field.value ?? ''}
                fullWidth
                label="ユーザ名"
                error={Boolean(errors.name)}
                helperText={errors.name?.message}
              />
            )}
          />
        </InputRow>

        <InputRow label="メールアドレス">
          <Controller
            control={control}
            name="email"
            render={({ field }) => (
              <TextField
                {...field}
                value={field.value ?? ''}
                fullWidth
                label="メールアドレス"
                error={Boolean(errors.email)}
                helperText={errors.email?.message}
              />
            )}
          />
        </InputRow>

        {/* パスワード */}
        <InputRow label="パスワード">
          <Controller
            control={control}
            name="password"
            render={({ field }) => (
              <TextField
                {...field}
                value={field.value ?? ''}
                type="password"
                fullWidth
                label="パスワード"
                error={Boolean(errors.password)}
                helperText={
                  errors.password?.message ??
                  (form_type === 'edit' ? '変更する場合のみ入力してください' : undefined)
                }
              />
            )}
          />
        </InputRow>

        {/* パスワード確認 */}
        <InputRow label="パスワード（確認）">
          <Controller
            control={control}
            name="password_confirmation"
            render={({ field }) => (
              <TextField
                {...field}
                value={field.value ?? ''}
                type="password"
                fullWidth
                label="パスワード（確認）"
                error={Boolean(errors.password_confirmation)}
                helperText={errors.password_confirmation?.message}
              />
            )}
          />
        </InputRow>

        <InputRow label="権限">
          <Controller
            control={control}
            name="role"
            render={({ field }) => (
              <Select
                {...field}
                value={field.value ?? ''}
                fullWidth
                label="権限"
                error={Boolean(errors.role)}
              >
                {options.roles.map((role) => (
                  <MenuItem key={role.id} value={role.id}>
                    {role.label}
                  </MenuItem>
                ))}
              </Select>
            )}
          />
        </InputRow>

        <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2, marginTop: 2 }}>
          <Button variant="contained" color="primary" type="submit" disabled={isSubmitting}>
            保存
          </Button>
          <Button
            variant="contained"
            color="secondary"
            type="button"
            onClick={() => router.visit(userList.url())}
            disabled={isSubmitting}
          >
            キャンセル
          </Button>
        </Box>
      </Box>
    </Box>
  );
}
