import { Head } from "@inertiajs/react";
import Box from "@mui/material/Box";
import Header from "@/components/ui/Header";
import ConsumptionList from "@/feature/consumption/ConsumptionList";
import type { ProductInventoryResource } from "@/types/resource";

interface ConsumptionIndexProps {
  products: ProductInventoryResource;
}

export default function ConsumptionIndex({ products }: ConsumptionIndexProps) {
  return (
    <>
      <Head title="商品販売" />
      <Header title="商品販売" />

      <Box
        sx={{
          py: 2,
        }}
      >
        <ConsumptionList products={products.data} />
      </Box>
    </>
  );
}