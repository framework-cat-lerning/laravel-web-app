import { Head } from "@inertiajs/react";
import Box from "@mui/material/Box";
import Header from "@/components/ui/Header";
import InventoryList from "@/feature/inventries/InventoryList";
import type { ProductInventoryResource } from "@/types";

interface InventoryIndexProps {
  inventories: ProductInventoryResource;
}

export default function InventoryIndex({ inventories }: InventoryIndexProps) {
  return (
    <>
      <Head title="在庫管理" />
      <Header title="在庫管理" />

      <Box
        sx={{
          py: 2,
        }}
      >
        <InventoryList inventries={inventories.data} />
      </Box>
    </>
  );
}