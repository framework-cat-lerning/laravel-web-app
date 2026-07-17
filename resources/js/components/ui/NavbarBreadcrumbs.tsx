import { styled } from '@mui/material/styles';
import Typography from '@mui/material/Typography';
import Breadcrumbs, { breadcrumbsClasses } from '@mui/material/Breadcrumbs';
import NavigateNextRoundedIcon from '@mui/icons-material/NavigateNextRounded';
import Link from '@mui/material/Link';

const StyledBreadcrumbs = styled(Breadcrumbs)(({ theme }) => ({
  margin: theme.spacing(1, 0),
  [`& .${breadcrumbsClasses.separator}`]: {
    color: (theme.vars || theme).palette.action.disabled,
    margin: 1,
  },
  [`& .${breadcrumbsClasses.ol}`]: {
    alignItems: 'center',
  },
}));

interface Parent {
  title: string;
  href: string;
}
interface NavbarBreadcrumbsProps {
  title: string;
  parents?: Parent[];
}

export default function NavbarBreadcrumbs({ title, parents=[] }: NavbarBreadcrumbsProps) {
  return (
    <StyledBreadcrumbs
      aria-label="breadcrumb"
      separator={<NavigateNextRoundedIcon fontSize="small" />}
    >
      <Typography variant="body1">Dashboard</Typography>
      {parents?.map((parent) => (
        <Typography variant="body1" key={parent.href}>
          <Link href={parent.href}>{parent.title}</Link>
        </Typography>
      ))}
      <Typography variant="body1" sx={{ color: 'text.primary', fontWeight: 600 }}>
        {title}
      </Typography>
    </StyledBreadcrumbs>
  );
}
