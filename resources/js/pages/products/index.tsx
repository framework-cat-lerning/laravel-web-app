import { Head } from '@inertiajs/react';
import Header from '@/components/ui/Header';
import Box from '@mui/material/Box';
import ProductList from '@/feature/products/ProductList';
import { Product } from '@/types';

interface ProductsIndexProps {
  products: Product[];
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
        <ProductList products={products} />
      </Box>
    </>
  );
}
