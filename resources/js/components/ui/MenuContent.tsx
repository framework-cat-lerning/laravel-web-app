import { useEffect } from 'react';
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


const systemAdminListItems = [
  { text: 'ダッシュボード', icon: <HomeRoundedIcon /> },
  { text: '商品管理', icon: <InventoryIcon /> },
  { text: 'ユーザ管理', icon: <PeopleAltIcon /> },
  { text: '追加申請', icon: <FileUploadIcon /> },
];

const importerListItems = [
  { text: 'ダッシュボード', icon: <HomeRoundedIcon /> },
  { text: '在庫管理', icon: <InventoryIcon /> },
  { text: '追加申請', icon: <FileUploadIcon /> },
];

const userListItems = [
  { text: 'ダッシュボード', icon: <HomeRoundedIcon /> },
  { text: '商品放出', icon: <FileDownloadIcon /> },

];

export default function MenuContent() {
  const { auth } = useAuth();
  let mainListItems: { text: string; icon: React.ReactNode }[] = [];

  if (auth.user.role === 1) {
    mainListItems = systemAdminListItems;
  } else if (auth.user.role === 2) {
    mainListItems = importerListItems;
  } else if (auth.user.role === 3) {
    mainListItems = userListItems;
  }

  return (
    <Stack sx={{ mt: 2, flexGrow: 1, p: 1, justifyContent: 'space-between' }}>
      <List dense>
        {mainListItems.map((item, index) => (
          <ListItem key={index} disablePadding sx={{ display: 'block' }}>
            <ListItemButton selected={index === 0}>
              <ListItemIcon>{item.icon}</ListItemIcon>
              <ListItemText primary={item.text} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>
    </Stack>
  );
}
