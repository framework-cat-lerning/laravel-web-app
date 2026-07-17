import { Head } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Header from '@/components/ui/Header';
import ProductFormComponent from '@/feature/products/ProductForm';
import type { ProductFormOutput } from '@/schemes/product';

interface ProductFormProps {
  form_type: 'new' | 'edit';
  product?: ProductFormOutput;
}

export default function ProductForm({ form_type, product }: ProductFormProps) {
  return (
    <>
      <Head title={form_type === 'new' ? '商品追加' : '商品編集'} />
      <Header title={form_type === 'new' ? '商品追加' : '商品編集'} />
      <Box
        sx={{
          py: 2,
        }}
      >
        <ProductFormComponent form_type={form_type} product={product} />
      </Box>
    </>
  );
}
