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
import { AdminComponent } from './pages/admin/admin.component';
import { AuditComponent } from './pages/audit/audit.component';
import { FirstTimeLoginComponent } from './pages/first-time-login/first-time-login.component';
import { ForgotPasswordComponent } from './pages/forgot-password/forgot-password.component';
import { VerifyOtpComponent } from './pages/verify-otp/verify-otp.component';
import { SuperAdminLoginComponent } from './pages/super-admin-login/super-admin-login.component';
import { AirlinesComponent } from './pages/airlines/airlines.component';
import { GalleryComponent } from './pages/gallery/gallery.component';
import { OpsSecurityComponent } from './pages/ops-security/ops-security.component';
import { OpsiSecurityComponent } from './pages/opsi-security/opsi-security.component';
import { CateringComponent } from './pages/catering/catering.component';
import { AirportComponent } from './pages/airport/airport.component';
import { AstiComponent } from './pages/asti/asti.component';
import { AvsecTrainingCalenderComponent } from './pages/avsec-training-calender/avsec-training-calender.component';
import { QuizComponent } from './pages/quiz/quiz.component';
import { OrganizationStructureComponent } from './pages/organization-structure/organization-structure.component';
import { QuizResultComponent } from './pages/quiz-result/quiz-result.component';
import { QuarterlyReportOnlineComponent } from './pages/quarterly-report-online/quarterly-report-online.component';
import { QuarterlyReportOnlineiiComponent } from './pages/quarterly-report-onlineii/quarterly-report-onlineii.component';


export const routes: Routes = [
  { path: 'dashboard', component: DasboardComponent, canActivate: [AuthGuard], data: { roles: ['restricted admin', 'superadmin'] } },
    { path: 'acts-and-policies', component: ActsAndPoliciesComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'circulars', component: CircularsComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] }  },
    { path: 'events', component: EventsComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] }  },
    { path: 'forms', component: FormsComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] }  },
    { path: 'tenders', component: TendersComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'notices', component: NoticesComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'vacancies', component: VacanciesComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'division', component: DivisionComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'region', component: RegionComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'common-title', component: CommonTitleComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'menu', component: MenuComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'admin/roles', component: RolesComponent, canActivate: [AuthGuard], data: { roles: ['superadmin'] } },
    { path: 'slider', component: SliderComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'slide', component: SlideComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'contact', component: ContactComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'division-gallery', component: DivisionGalleryComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'admin-doc', component: AdminDocumentComponent, canActivate: [AuthGuard], data: { roles: ['restricted admin', 'superadmin'] } },
    { path: 'admin-doc-categories', component: AdminDocumentCategoryComponent, canActivate: [AuthGuard], data: { roles: ['superadmin'] } },
    { path: 'user', component: AdminComponent, canActivate: [AuthGuard], data: { roles: ['superadmin'] } },
    { path: '', component: LoginComponent, canActivate: [NoAuthGuard] },
    { path: 'super-admin-login', component: SuperAdminLoginComponent, canActivate: [NoAuthGuard]  },
    { path: 're-registration', component: FirstTimeLoginComponent, canActivate: [NoAuthGuard]  },
    { path: 'forget-password', component: ForgotPasswordComponent, canActivate: [NoAuthGuard]  },
    { path: 'verify-otp', component: VerifyOtpComponent },
    { path: 'audit', component: AuditComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'airlines', component: AirlinesComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'gallery', component: GalleryComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'ops-security', component: OpsSecurityComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'opsi-security', component: OpsiSecurityComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'catering', component: CateringComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'airport', component: AirportComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'asti', component: AstiComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'avsec-training-calender', component: AvsecTrainingCalenderComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'quiz', component: QuizComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'organization-structure', component: OrganizationStructureComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'quiz-result', component: QuizResultComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'quarterly-reoprt-online', component: QuarterlyReportOnlineComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: 'quarterly-reoprt-online-2', component: QuarterlyReportOnlineiiComponent, canActivate: [AuthGuard], data: { roles: ['CMS Admin', 'superadmin'] } },
    { path: '**', redirectTo: '/' },
    {
      path: 'restricted',
      loadChildren: () => import('./restricted/restricted.module').then(m => m.RestrictedModule)
    },
];

