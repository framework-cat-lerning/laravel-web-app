import Stack from '@mui/material/Stack';
import NotificationsRoundedIcon from '@mui/icons-material/NotificationsRounded';
import CustomDatePicker from './CustomDatePicker';
import NavbarBreadcrumbs from './NavbarBreadcrumbs';
import MenuButton from './MenuButton';
import ColorModeIconDropdown from '../../themes/shared/ColorModeIconDropdown';

import Search from './Search';

interface Parent {
  title: string;
  href: string;
}
interface HeaderProps {
  title: string;
  isSearch?: boolean;
  parents?: Parent[];
}

export default function Header({ title, isSearch = false, parents=[] }: HeaderProps) {
  return (
    <Stack
      direction="row"
      sx={{
        display: { xs: 'none', md: 'flex' },
        width: '100%',
        alignItems: { xs: 'flex-start', md: 'center' },
        justifyContent: 'space-between',
        maxWidth: { sm: '100%', md: '1700px' },
        pt: 1.5,
      }}
      spacing={2}
    >
      <NavbarBreadcrumbs title={title} parents={parents} />
      <Stack direction="row" sx={{ gap: 1 }}>
        {isSearch ? (
          <>
            <Search />
            <CustomDatePicker />
            <MenuButton showBadge aria-label="Open notifications">
              <NotificationsRoundedIcon />
            </MenuButton>
            <ColorModeIconDropdown />
          </>
        ) : (
          <></>
        )}
      </Stack>
    </Stack>
  );
}
