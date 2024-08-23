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

export const routes: Routes = [
    { path: 'acts-and-policies', component: ActsAndPoliciesComponent },
    { path: 'circulars', component: CircularsComponent },
    { path: 'events', component: EventsComponent },
    { path: 'forms', component: FormsComponent },
    { path: 'tenders', component: TendersComponent },
    { path: 'notices', component: NoticesComponent },
    { path: 'vacancies', component: VacanciesComponent },
    { path: 'division', component: DivisionComponent },
    { path: 'region', component: RegionComponent },
];

