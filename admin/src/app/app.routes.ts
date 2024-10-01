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
import { LoginComponent } from './pages/auth/login/login.component';
import { AuthGuard } from './guards/auth.guard';

export const routes: Routes = [
    { path: 'acts-and-policies', component: ActsAndPoliciesComponent, canActivate: [AuthGuard]  },
    { path: 'circulars', component: CircularsComponent, canActivate: [AuthGuard]  },
    { path: 'events', component: EventsComponent, canActivate: [AuthGuard]  },
    { path: 'forms', component: FormsComponent, canActivate: [AuthGuard]  },
    { path: 'tenders', component: TendersComponent, canActivate: [AuthGuard]  },
    { path: 'notices', component: NoticesComponent, canActivate: [AuthGuard]  },
    { path: 'vacancies', component: VacanciesComponent, canActivate: [AuthGuard]  },
    { path: 'division', component: DivisionComponent, canActivate: [AuthGuard]  },
    { path: 'region', component: RegionComponent, canActivate: [AuthGuard]  },
    { path: 'login', component: LoginComponent  },
    { path: '**', redirectTo: '/login' }
];

