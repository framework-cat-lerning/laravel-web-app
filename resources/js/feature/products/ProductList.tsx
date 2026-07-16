import TableContainer from '@mui/material/TableContainer';
import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableHead from '@mui/material/TableHead';
import TableBody from '@mui/material/TableBody';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import { Product } from '@/types';
import Button from '@mui/material/Button';

interface ProductListProps {
  products: Product[];
}

export default function ProductList({ products }: ProductListProps) {
  return (
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
                <Button variant="contained" color="primary" onClick={() => {}}>
                  承認
                </Button>
                <Button variant="contained" color="primary">
                  編集
                </Button>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  );
}