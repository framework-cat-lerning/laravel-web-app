import { zodResolver } from '@hookform/resolvers/zod';
import { router, usePage } from '@inertiajs/react';
import Alert from '@mui/material/Alert';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import InputAdornment from '@mui/material/InputAdornment';
import TextField from '@mui/material/TextField';
import Text from '@mui/material/Typography';
import { useEffect, useState } from 'react';
import { Controller, useForm  } from 'react-hook-form';
import type {UseFormSetError} from 'react-hook-form';
import { index as productList, store as productStore, update as productUpdate } from '@/routes/staff/products';
import { productFormScheme   } from '@/schemes/product';
import type {ProductFormInput, ProductFormOutput} from '@/schemes/product';

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
        router.put(productUpdate.url({ product: product?.id ?? 0 }), data, options);
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

        <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2 }}>
          <Text sx={{ fontSize: 16, fontWeight: 'bold', flex: 1 }}>
            商品名
          </Text>
          <Box sx={{ flex: 10 }}>
            <Controller
              control={control}
              name="name"
              render={({ field }) => (
                <TextField
                  {...field}
                  value={field.value ?? ''}
                  fullWidth
                  label="商品名"
                  error={Boolean(errors.name)}
                  helperText={errors.name?.message}
                />
              )}
            />
          </Box>
        </Box>

        <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2, marginTop: 2 }}>
          <Text sx={{ fontSize: 16, fontWeight: 'bold', flex: 1 }}>
            商品説明
          </Text>
          <Box sx={{ flex: 10 }}>
            <Controller
              control={control}
              name="description"
              render={({ field }) => (
                <TextField
                  {...field}
                  value={field.value ?? ''}
                  fullWidth
                  multiline
                  minRows={4}
                  label="商品説明"
                  error={Boolean(errors.description)}
                  helperText={errors.description?.message}
                  sx={{
                    '& .MuiInputBase-root': {
                      alignItems: 'flex-start',
                    },
                  }}
                />
              )}
            />
          </Box>
        </Box>

        <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2, marginTop: 2 }}>
          <Text sx={{ fontSize: 16, fontWeight: 'bold', flex: 1 }}>
            商品価格
          </Text>
          <Box sx={{ flex: 10 }}>
            <Controller
              control={control}
              name="price"
              render={({ field }) => (
                <TextField
                  name={field.name}
                  onBlur={field.onBlur}
                  inputRef={field.ref}
                  value={field.value ?? ''}
                  onChange={(event) => {
                    const value = event.target.value;
                    field.onChange(value === '' ? '' : Number(value));
                  }}
                  type="number"
                  fullWidth
                  label="商品価格"
                  slotProps={{
                    input: {
                      startAdornment: <InputAdornment position="start">¥</InputAdornment>,
                    },
                  }}
                  error={Boolean(errors.price)}
                  helperText={errors.price?.message}
                />
              )}
            />
          </Box>
        </Box>

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
