import type { ProductInventory } from "@/types";
import Box from "@mui/material/Box";
import Table from "@mui/material/Table";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";
import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import TableBody from "@mui/material/TableBody";
import CardActions from "@mui/material/CardActions";
import Button from "@mui/material/Button";
import { useState } from "react";
import Dialog from "@mui/material/Dialog";
import DialogTitle from "@mui/material/DialogTitle";
import DialogContent from "@mui/material/DialogContent";
import TextField from "@mui/material/TextField";
import DialogActions from "@mui/material/DialogActions";
import { router } from "@inertiajs/react";
import {  buy } from "@/routes/staff/inventries";

interface InventoryListProps {
  inventries: ProductInventory[];
}

export default function InventoryList({ inventries }: InventoryListProps) {
  const [selectedInventory, setSelectedInventory] = useState<ProductInventory | null>(null);
  const [count, setCount] = useState<number>(0);

  const showBuyDialog = (inventory: ProductInventory) => {
    setSelectedInventory(inventory);
    setCount(0);
  };

  const handleBuy = (inventory: ProductInventory) => {
    if (count <= 0) {
      alert('数量を入力してください');
      return;
    }
    router.post(buy.url({ product: inventory.id }), { count: count }, {
      onSuccess: () => {
        setSelectedInventory(null);
        setCount(0);
        alert('追加購入が完了しました');
      },
    });
  };

  return (
    <Box>
      <Typography variant="h6">在庫管理</Typography>

      <Box sx={{ display: 'flex', flexDirection: 'row', gap: 2 }}>
      {inventries.map((inventory) => (
        <Card key={inventory.id} variant="outlined" sx={{ flex: '1 1 50%' }}>
          <CardContent>
            <Typography variant="subtitle1" sx={{ color: 'text.secondary' }}>
              {inventory.name}
            </Typography>
            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              {inventory.description || '説明がありません'}
            </Typography>
            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              在庫数：<Typography variant="button" gutterBottom>{inventory.quantity}</Typography>
            </Typography>
          </CardContent>
          <CardActions sx={{ justifyContent: 'flex-end' }}>
            <Button variant="contained" color="primary" onClick={() => showBuyDialog(inventory)}>
              購入
            </Button>
          </CardActions>
        </Card>
      ))}
      </Box>

      {selectedInventory && (
        <Dialog open={!!selectedInventory} onClose={() => setSelectedInventory(null)}>
          <DialogTitle>購入</DialogTitle>
          <DialogContent>
            <TextField label="数量" type="number" value={count} onChange={(e) => setCount(parseInt(e.target.value))} />
          </DialogContent>
          <DialogActions>
            <Button variant="contained" color="primary" onClick={() => handleBuy(selectedInventory)}>
              購入
            </Button>
          </DialogActions>
        </Dialog>
      )}
    </Box>
  );
}