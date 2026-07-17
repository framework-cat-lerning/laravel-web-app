import { Link } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import { useAuth } from '@/contexts/AuthContext';
import { newMethod as productCreate } from '@/routes/staff/products';
import type { Product } from '@/types';

interface ProductListProps {
  products: Product[];
}

export default function ProductList({ products }: ProductListProps) {
  const { auth: { user } } = useAuth();

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
                <TableCell>{product.created_at}</TableCell>
                <TableCell>{product.updated_at}</TableCell>
                <TableCell>
                  <Button variant="contained" color="primary" sx={{ marginRight: 1 }} onClick={() => { }}>
                    承認
                  </Button>
                  <Button variant="contained" color="warning" sx={{ marginRight: 1 }}>
                    編集
                  </Button>
                  <Button variant="contained" color="error" sx={{ marginRight: 1 }}>
                    キャンセル
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </>
  );
}