import { Head } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Header from '@/components/ui/Header';
import ProductDetail from '@/feature/products/ProductDetail';
import { index } from '@/routes/admin/products';
import type { Product } from '@/types';

interface ProductShowProps {
  product: {
    data: Product;
  };
}

export default function ProductShow({ product }: ProductShowProps) {
  return (
    <>
      <Head title="ユーザー管理" />
      <Header title="ユーザー詳細" parents={[{ title: 'ユーザー管理', href: index.url() }]} />
      <Box
        sx={{
          py: 2,
        }}
      >
        <ProductDetail product={product.data} />
      </Box>
    </>
  );
}
