import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import DialogContentText from '@mui/material/DialogContentText';
import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Typography from '@mui/material/Typography';
import { useState } from 'react';
import ConfirmDialog from '@/components/ui/ConfirmDialog';
import { useAuth } from '@/contexts/AuthContext';
import { approval, show } from '@/routes/admin/products';
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

  const onShowClick = (product: Product) => {
    if (!user || user.role !== 1) {
      alert('権限がありません');

      return;
    }

    router.visit(show.url({ product: product.id }));
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
                        <Button variant="contained" color="secondary" sx={{ marginRight: 1 }} onClick={() => onShowClick(product)}>
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

      <ConfirmDialog
        open={!!approvalTarget}
        title="承認確認ダイアログ"
        message={
          <>
            <DialogContentText>以下の商品を承認しますか？</DialogContentText>
            <Typography variant="body1">{approvalTarget?.name}</Typography>
          </>
        }
        onOK={handleApproveConfirm}
        onCancel={handleApproveClose}
      />

      <ConfirmDialog
        open={!!cancelTarget}
        title="キャンセル確認ダイアログ"
        message={
          <>
            <DialogContentText>以下の商品の申請をキャンセルしますか？</DialogContentText>
            <Typography variant="body1">{cancelTarget?.name}</Typography>
          </>
        }
        onOK={handleCancelConfirm}
        onCancel={handleCancelClose}
      />
    </>
  );
}