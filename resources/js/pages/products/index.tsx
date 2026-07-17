import { Head } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Header from '@/components/ui/Header';
import ProductList from '@/feature/products/ProductList';
import type { ProductResource } from '@/types';

interface ProductsIndexProps {
  products: ProductResource;
}

export default function ProductsIndex({ products }: ProductsIndexProps) {
  return (
    <>
      <Head title="商品管理" />
      <Header title="商品管理" />
      <Box
        sx={{
          py: 2,
        }}
      >
        <ProductList products={products.data} />
      </Box>
    </>
  );
}
