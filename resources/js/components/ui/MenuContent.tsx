import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import Stack from '@mui/material/Stack';
import HomeRoundedIcon from '@mui/icons-material/HomeRounded';
import InventoryIcon from '@mui/icons-material/Inventory';
import PeopleAltIcon from '@mui/icons-material/PeopleAlt';
import FileUploadIcon from '@mui/icons-material/FileUpload';
import FileDownloadIcon from '@mui/icons-material/FileDownload';
import { useAuth } from '../../contexts/AuthContext';
import { dashboard } from '@/routes';
import { index as adminProductsIndex } from '@/routes/admin/products';
import { index as adminUsersIndex } from '@/routes/admin/users';
import { index as staffProductsIndex } from '@/routes/staff/products';
import { index as inventoryIndex } from '@/routes/staff/inventries';
import { index as shopProductsIndex } from '@/routes/shop/products';

const systemAdminListItems = [
  { text: 'ダッシュボード', icon: <HomeRoundedIcon />, href: dashboard.url() },
  { text: '商品管理', icon: <InventoryIcon />, href: adminProductsIndex.url() },
  { text: 'ユーザ管理', icon: <PeopleAltIcon />, href: adminUsersIndex.url() },
];

const staffListItems = [
  { text: 'ダッシュボード', icon: <HomeRoundedIcon />, href: dashboard.url() },
  { text: '在庫管理', icon: <InventoryIcon />, href: inventoryIndex.url() },
  { text: '追加申請', icon: <FileUploadIcon />, href: staffProductsIndex.url() },
];

const shopListItems = [
  { text: 'ダッシュボード', icon: <HomeRoundedIcon />, href: dashboard.url() },
  { text: '商品販売', icon: <FileDownloadIcon />, href: shopProductsIndex.url() },

];

export default function MenuContent() {
  const { auth } = useAuth();
  let mainListItems: { text: string; icon: React.ReactNode; href: string }[] = [];

  if (auth.user.role === 1) {
    mainListItems = systemAdminListItems;
  } else if (auth.user.role === 2) {
    mainListItems = staffListItems;
  } else if (auth.user.role === 3) {
    mainListItems = shopListItems;
  }

  return (
    <Stack sx={{ mt: 2, flexGrow: 1, p: 1, justifyContent: 'space-between' }}>
      <List dense>
        {mainListItems.map((item, index) => (
          <ListItem key={index} disablePadding sx={{ display: 'block' }}>
            <ListItemButton selected={index === 0} href={item.href}>
              <ListItemIcon>{item.icon}</ListItemIcon>
              <ListItemText primary={item.text} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>
    </Stack>
  );
}
