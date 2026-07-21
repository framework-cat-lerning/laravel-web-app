import { router } from "@inertiajs/react";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import CardActions from "@mui/material/CardActions";
import CardContent from "@mui/material/CardContent";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogTitle from "@mui/material/DialogTitle";
import TextField from "@mui/material/TextField";
import Typography from "@mui/material/Typography";
import { useState } from "react";
import {  buy } from "@/routes/staff/inventries";
import type { ProductInventory } from "@/types";

interface ConsumptionListProps {
  products: ProductInventory[];
}

export default function ConsumptionList({ products }: ConsumptionListProps) {
  const [selectedProduct, setSelectedProduct] = useState<ProductInventory | null>(null);
  const [count, setCount] = useState<number>(0);

  const showBuyDialog = (product: ProductInventory) => {
    setSelectedProduct(product);
    setCount(0);
  };

  const handleBuy = (product: ProductInventory) => {
    if (count <= 0) {
      alert('数量を入力してください');

      return;
    }

    router.post(buy.url({ product: product.id }), { count: count }, {
      onSuccess: () => {
        setSelectedProduct(null);
        setCount(0);
        alert('追加購入が完了しました');
      },
    });
  };

  return (
    <Box>
      <Typography variant="h6">商品販売</Typography>

      <Box sx={{ display: 'flex', flexDirection: 'row', gap: 2 }}>
      {products.map((product) => (
        <Card key={product.id} variant="outlined" sx={{ flex: '1 1 50%' }}>
          <CardContent>
            <Typography variant="subtitle1" sx={{ color: 'text.secondary' }}>
              {product.name}
            </Typography>
            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              {product.description || '説明がありません'}
            </Typography>
            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              在庫数：<Typography variant="button" gutterBottom>{product.quantity}</Typography>
            </Typography>
          </CardContent>
          <CardActions sx={{ justifyContent: 'flex-end' }}>
            <Button variant="contained" color="primary" onClick={() => showBuyDialog(product)}>
              購入
            </Button>
          </CardActions>
        </Card>
      ))}
      </Box>

      {selectedProduct && (
        <Dialog open={!!selectedProduct} onClose={() => setSelectedProduct(null)}>
          <DialogTitle>商品販売</DialogTitle>
          <DialogContent>
            <TextField label="数量" type="number" value={count} onChange={(e) => setCount(parseInt(e.target.value))} />
          </DialogContent>
          <DialogActions>
            <Button variant="contained" color="primary" onClick={() => handleBuy(selectedProduct)}>
              販売
            </Button>
          </DialogActions>
        </Dialog>
      )}
    </Box>
  );
}