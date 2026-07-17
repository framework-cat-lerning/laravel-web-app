import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Typography from '@mui/material/Typography';
import { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { approval } from '@/routes/admin/products';
import { newMethod as productCreate } from '@/routes/staff/products';
import { cancel } from '@/routes/staff/products';
import type { Product } from '@/types';

interface ProductListProps {
  products: Product[];
}

export default function ProductList({ products }: ProductListProps) {
  const { auth: { user } } = useAuth();
  const [approvalTarget, setApprovalTarget] = useState<Product | null>(null);
  const [cancelTarget, setCancelTarget] = useState<Product | null>(null);

  const handleApproveClick = (product: Product) => {
    setApprovalTarget(product);
  };

  const handleApproveConfirm = () => {
    if (!approvalTarget) {
      return;
    }

    router.patch(approval.url({ product: approvalTarget.id }), {}, {
      onSuccess: () => {
        setApprovalTarget(null);
      },
    });
  };

  const handleApproveClose = () => {
    setApprovalTarget(null);
  };

  const handleCancelClick = (product: Product) => {
    setCancelTarget(product);
  };

  const handleCancelConfirm = () => {
    if (!cancelTarget) {
      return;
    }

    router.delete(cancel.url({ product: cancelTarget.id }), {
      onSuccess: () => {
        setCancelTarget(null);
      },
    });
  };

  const handleCancelClose = () => {
    setCancelTarget(null);
  };

  return (
    <>
      {user.role === 2 && (
        <Box sx={{ mb: 1, display: 'flex', justifyContent: 'flex-end', marginBottom: 2 }}>
          <Link href={productCreate.url()}>
            <Button variant="contained" color="primary">
              追加申請
            </Button>
          </Link>
        </Box>
      )}
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>商品名</TableCell>
              <TableCell>参考金額</TableCell>
              <TableCell>ステータス</TableCell>
              <TableCell>作成日</TableCell>
              <TableCell>更新日</TableCell>
              <TableCell>操作</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {products.length === 0 && (
              <tr>
                <td colSpan={6} className="text-center py-4">
                  データがありません
                </td>
              </tr>
            )}
            {products.map((product) => (
              <TableRow key={product.id}>
                <TableCell>{product.id}</TableCell>
                <TableCell>{product.name}</TableCell>
                <TableCell>{product.price}</TableCell>
                <TableCell>{product.status.label}</TableCell>
                <TableCell>{product.created_at}</TableCell>
                <TableCell>{product.updated_at}</TableCell>
                <TableCell>
                  {user.role === 1 && (
                    <>
                      {product.status.id === 1 && (
                        <Button variant="contained" color="success" sx={{ marginRight: 1 }} onClick={() => handleApproveClick(product)}>
                          承認
                        </Button>
                      )}
                      {product.status.id === 2 && (
                        <Button variant="contained" color="secondary" sx={{ marginRight: 1 }}>
                          編集
                        </Button>
                      )}
                    </>
                  )}
                  {user.role === 2 && (
                    <>
                      {product.status.id === 1 && (
                        <Button variant="contained" color="error" sx={{ marginRight: 1 }} onClick={() => handleCancelClick(product)}>
                          キャンセル
                        </Button>
                      )}
                      {product.status.id === 2 && (
                        <Typography variant="body1">操作不可</Typography>
                      )}
                    </>
                  )}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      <Dialog open={!!approvalTarget} onClose={handleApproveClose} maxWidth="sm" fullWidth>
        <DialogTitle>承認確認ダイアログ</DialogTitle>
        <DialogContent>
          <DialogContentText>以下の商品を承認しますか？</DialogContentText>
          <Typography variant="body1">{approvalTarget?.name}</Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleApproveClose}>キャンセル</Button>
          <Button onClick={handleApproveConfirm}>承認</Button>
        </DialogActions>
      </Dialog>

      <Dialog open={!!cancelTarget} onClose={handleCancelClose} maxWidth="sm" fullWidth>
        <DialogTitle>キャンセル確認ダイアログ</DialogTitle>
        <DialogContent>
          <DialogContentText>以下の商品の申請をキャンセルしますか？</DialogContentText>
          <Typography variant="body1">{cancelTarget?.name}</Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleCancelConfirm}>キャンセル</Button>
        </DialogActions>
      </Dialog>
    </>
  );
}