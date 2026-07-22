import { zodResolver } from '@hookform/resolvers/zod';
import { router, usePage } from '@inertiajs/react';
import Alert from '@mui/material/Alert';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import type { UseFormSetError } from 'react-hook-form';
import InputRow from '@/components/form/InputRow';
import { useAuth } from '@/contexts/AuthContext';
import { update as adminProductUpdate } from '@/routes/admin/products';
import { index as productList, store as productStore } from '@/routes/staff/products';
import { productFormScheme } from '@/schemes/product';
import type { ProductFormInput, ProductFormOutput } from '@/schemes/product';

interface ProductFormProps {
  form_type: 'new' | 'edit';
  product?: ProductFormOutput;
}

const FIELD_NAMES = ['name', 'description', 'price'] as const;

function applyServerErrors(
  serverErrors: Record<string, string>,
  setError: UseFormSetError<ProductFormInput>,
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

export default function ProductForm({ form_type, product }: ProductFormProps) {
  const { errors: pageErrors } = usePage().props;
  const { auth } = useAuth();
  const {
    control,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<ProductFormInput, unknown, ProductFormOutput>({
    resolver: zodResolver(productFormScheme),
    defaultValues: {
      name: product?.name ?? '',
      description: product?.description ?? '',
      price: product?.price ?? '',
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

  const onSubmit = handleSubmit(async (data: ProductFormOutput) => {
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
        router.post(productStore.url(), data, options);
      } else {
        // ユーザが管理者なら管理者用の更新APIを呼び出す
        if (auth.user.role === 1) {
          router.put(adminProductUpdate.url({ product: product?.id ?? 0 }), data, options);
        } else {
          // 管理者以外の場合は一旦更新処理はできない
        }
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

        {/** 商品名 */}
        <InputRow
          label="商品名"
          input_name="name"
          target_errors={errors.name ? { message: errors.name.message as string } : undefined}
          target_control={control}
        />

        {/** 商品説明 */}
        <InputRow
          label="商品説明"
          input_name="description"
          target_errors={errors.description ? { message: errors.description.message as string } : undefined}
          target_control={control}
        />

        {/** 商品価格 */}
        <InputRow
          label="商品価格"
          target_control={control}
          input_name="price"
          target_errors={errors.price ? { message: errors.price.message as string } : undefined}
        />

        <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2, marginTop: 2 }}>
          <Button variant="contained" color="primary" type="submit" disabled={isSubmitting}>
            保存
          </Button>
          <Button
            variant="contained"
            color="secondary"
            type="button"
            onClick={() => router.visit(productList.url())}
            disabled={isSubmitting}
          >
            キャンセル
          </Button>
        </Box>
      </Box>
    </Box>
  );
}
