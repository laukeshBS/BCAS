import { RouterModule, Routes } from '@angular/router';
import { ActsAndPoliciesComponent } from './pages/acts-and-policies/acts-and-policies.component';
import { CircularsComponent } from './pages/circulars/circulars.component';
import { EventsComponent } from './pages/events/events.component';
import { FormsComponent } from './pages/forms/forms.component';
import { TendersComponent } from './pages/tenders/tenders.component';
import { NoticesComponent } from './pages/notices/notices.component';
import { VacanciesComponent } from './pages/vacancies/vacancies.component';
import { DivisionComponent } from './pages/division/division.component';
import { RegionComponent } from './pages/region/region.component';
import { CommonTitleComponent } from './pages/common-title/common-title.component';
import { LoginComponent } from './pages/auth/login/login.component';
import { AuthGuard } from './guards/auth.guard';
import { MenuComponent } from './pages/menu/menu.component';
import { SliderComponent } from './pages/slider/slider.component';
import { SlideComponent } from './pages/slide/slide.component';
import { NoAuthGuard } from './guards/no-auth.guard';

import { DivisionGalleryComponent } from './pages/division-gallery/division-gallery.component';
import { AdminDocumentComponent } from './pages/admin-document/admin-document.component';

import { RolesComponent } from './pages/roles/roles.component';
import { AdminDocumentCategoryComponent } from './pages/admin-document-category/admin-document-category.component';
import { DasboardComponent } from './pages/dasboard/dasboard.component';
import { ContactComponent } from './pages/contact/contact.component';


export const routes: Routes = [
  { path: 'dashboard', component: DasboardComponent, canActivate: [AuthGuard]  },
    { path: 'acts-and-policies', component: ActsAndPoliciesComponent, canActivate: [AuthGuard]  },
    { path: 'circulars', component: CircularsComponent, canActivate: [AuthGuard]  },
    { path: 'events', component: EventsComponent, canActivate: [AuthGuard]  },
    { path: 'forms', component: FormsComponent, canActivate: [AuthGuard]  },
    { path: 'tenders', component: TendersComponent, canActivate: [AuthGuard]  },
    { path: 'notices', component: NoticesComponent, canActivate: [AuthGuard]  },
    { path: 'vacancies', component: VacanciesComponent, canActivate: [AuthGuard]  },
    { path: 'division', component: DivisionComponent, canActivate: [AuthGuard]  },
    { path: 'region', component: RegionComponent, canActivate: [AuthGuard]  },
    { path: 'common-title', component: CommonTitleComponent, canActivate: [AuthGuard]  },
    { path: 'menu', component: MenuComponent, canActivate: [AuthGuard]  },
    { path: 'admin/roles', component: RolesComponent, canActivate: [AuthGuard]  },
    { path: 'slider', component: SliderComponent, canActivate: [AuthGuard]  },
    { path: 'slide', component: SlideComponent, canActivate: [AuthGuard]  },
    { path: 'contact', component: ContactComponent, canActivate: [AuthGuard]  },
    { path: 'division-gallery', component: DivisionGalleryComponent, canActivate: [AuthGuard]  },
    { path: 'admin-doc', component: AdminDocumentComponent, canActivate: [AuthGuard]  },
    { path: 'admin-doc-categories', component: AdminDocumentCategoryComponent, canActivate: [AuthGuard]  },
    { path: 'login', component: LoginComponent, canActivate: [NoAuthGuard]  },
    { path: '**', redirectTo: '/login' },
    {
      path: 'restricted',
      loadChildren: () => import('./restricted/restricted.module').then(m => m.RestrictedModule)
    },
];

