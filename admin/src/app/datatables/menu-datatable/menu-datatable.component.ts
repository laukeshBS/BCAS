import { Component } from '@angular/core';
import { MenuService } from '../../services/menu.service';  // Import the service
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpEvent, HttpEventType } from '@angular/common/http';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { LanguageService } from '../../services/language.service';
import { environment } from '../../environments/environment';

declare var bootstrap: any;

@Component({
  selector: 'app-menu-datatable',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './menu-datatable.component.html',
  styleUrl: './menu-datatable.component.css'
})
export class MenuDatatableComponent {
  apiDocBaseUrl: string = environment.apiDocBaseUrl;
  events: any[] = [];
  selectedMenuType: any;// Property to hold selected menu type
  selectedEvent: any = { menu_child_id: 0 };
  fileToUpload: File | null = null;
  limit = 10; 
  lang_code = 'en'; 
  selectedFile: any;
  selectedFileError: string | null = null; // Initialized with null
  currentPage: number = 1; // Current page for pagination
  totalItems: number = 0; // Total items to calculate total pages
  loading: boolean = false;
  lastPage: number = 0; // Last page
  fileToUploadDoc: File | null = null;
  fileToUploadBanner: File | null = null;
  fileToUploadImg: File | null = null;
  userId: number | null = null;
  language_id: string = ''; 
  languages: any;
  PrimaryLink:any[] = [];
  pagesStatus: any;

  constructor(private MenuService: MenuService,private sanitizer: DomSanitizer
    ,private languageService: LanguageService,) {}

  ngOnInit(): void {
   // this.loadList();
    this.loadUserId();
    this.loadLangList();
    this.onlangChange(this.lang_code);
  }
 
  loadList(langCode:any): void {
    this.loading = true; // Start loading
    this.lang_code=langCode;
    this.MenuService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
      this.events = data.data;
      this.totalItems = data.total; // Assuming the API returns total items
      this.lastPage = Math.ceil(this.totalItems / this.limit);
      //console.log(this.totalItems);
      this.formatEventDates();
      this.loading = false; // Stop loading
    }, error => {
      console.error('Error loading events:', error);
      this.loading = false; // Stop loading on error
    });
  }
  loadLangList(): void {
    this.loading = true; // Start loading
    this.languageService.languagesList().subscribe({
      next: (data: any) => {
        this.languages = data.data;
      },
      error: (error: any) => {
        //this.loadingService.hide();
        this.loading = true;
        console.error('Error fetching languages', error);
      }
    });
  }
  onlangChange(value:any){
    this.loadList(value);
    this.PrimaryLink =  this.events;
  //console.log(value);
    this.lang_code=value;
    this.loading = true; // Start loading
    this.MenuService.allList(this.limit, this.lang_code, this.currentPage).subscribe(data => {
      this.PrimaryLink = data.data;
      this.formatEventDates();
      this.loading = false; // Stop loading
    }, error => {
      console.error('Error loading events:', error);
      this.loading = false; // Stop loading on error
    });
    
 }
  loadChidedList(pageID:any): void {
    //alert(pageID);
    this.loading = true; // Start loading
    this.MenuService.loadChidedList(this.limit, this.lang_code, this.currentPage,pageID).subscribe(data => {
      this.events = data.data;
      this.totalItems = data.total; // Assuming the API returns total items
      this.lastPage = Math.ceil(this.totalItems / this.limit);
      //this.formatEventDates();
      this.loading = false; // Stop loading
    }, error => {
      console.error('Error loading events:', error);
      this.loading = false; // Stop loading on error
    });
  }
  BackEvent(){
    //this.router.navigate([`/menu/`]);
    location.reload();
  }
  // Change page method
  changePage(page: number): void {
    //('Changing to page:', page); // Debugging line
    if (page < 1 || page > this.lastPage) return; // Prevent out of bounds
    this.currentPage = page;
    this.loadList(this.lang_code); // Reload data
  }

  // Total pages calculation
  totalPages(): number {
    return Math.ceil(this.totalItems / this.limit);
  }

  formatEventDates(): void {
    //console.log(this.events);
    this.events.forEach(event => {
      event.created_at = new Date(event.created_at).toLocaleDateString('en-GB');
    
     this.pageStatus(event.approve_status)
     switch (event.approve_status) {
      case 3:
        event.status = 'Published';
          break;
      case 2:
        event.status = 'Pending';
          break;
      default:
        event.status = 'Draft';
          break;
       }
      if (event.document!='') {
        event.document = '<a href="'+event.document+'">'+event.title+' Document</a>';
      }else{
        event.document = '';
      }
    });
  }
  pageStatus(status:any){
    // console.log('statoo11'+status);
    // console.log('statoo'+this.pagesStatus);
        switch (status) {
          case 3:
              this.pagesStatus = 'Published';
              break;
          case 2:
              this.pagesStatus = 'Pending';
              break;
          default:
              this.pagesStatus = 'Draft';
              break;
      }
  }

  editEvent(id: number): void {
    this.MenuService.getEvent(id).subscribe(data => {
      this.selectedEvent = data;
     // console.log(this.selectedEvent);
      this.openEditModal();
    });
  }

  addEvent(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  saveEvent(): void {
    // // Validate the form data
    if (!this.selectedEvent.menu_type || !this.selectedEvent.language_id || !this.selectedEvent.menu_position || !this.selectedEvent.menu_name || !this.selectedEvent.welcomedescription || !this.selectedEvent.menu_url  || !this.selectedEvent.approve_status ) {
      console.error('Missing required fields');
      return;
    }
    
     //console.log(this.selectedEvent);
    const formData = new FormData();
    formData.append('menu_type', this.selectedEvent.menu_type);
    formData.append('content', this.selectedEvent.content);
    formData.append('language_id', this.selectedEvent.language_id);
    formData.append('menu_position', this.selectedEvent.menu_position);
    formData.append('menu_name', this.selectedEvent.menu_name);
    formData.append('page_order', this.selectedEvent.page_order);
    formData.append('welcomedescription', this.selectedEvent.welcomedescription);
    formData.append('menu_url', this.selectedEvent.menu_url);
    formData.append('menu_link', this.selectedEvent.menu_link);
    formData.append('approve_status', this.selectedEvent.approve_status);
    formData.append('menu_description', this.selectedEvent.menu_description);
    formData.append('menu_child_id', this.selectedEvent.menu_child_id);
    if (this.fileToUploadDoc) {
      formData.append('doc_upload', this.fileToUploadDoc, this.fileToUploadDoc.name);
    }
    if (this.fileToUploadBanner) {
      formData.append('banner_img', this.fileToUploadBanner, this.fileToUploadBanner.name);
    }
    if (this.fileToUploadImg) {
      formData.append('img_upload', this.fileToUploadImg, this.fileToUploadImg.name);
    }
    if (this.userId) {
      formData.append('created_by', this.userId.toString()); // Convert to string
    }
   // console.log(formData);
    this.MenuService.storeEvent(formData).subscribe(
      (event: HttpEvent<any>) => {
          this.loadList(this.lang_code); // Refresh the list of events
          this.closeAddModal(); // Close the modal or form
      },
      error => {
        console.error('Error saving event', error);
      }
    );
  }

  modifyEvent(): void {
    //console.log(this.selectedEvent);
    // Validate the form data
    if (!this.selectedEvent.menu_type || !this.selectedEvent.menu_position || !this.selectedEvent.language_id || !this.selectedEvent.menu_name  || !this.selectedEvent.welcomedescription || !this.selectedEvent.menu_url  || !this.selectedEvent.approve_status ) {
      console.error('Missing required fields');
      return;
    }

    const formData = new FormData();
    formData.append('menu_type', this.selectedEvent.menu_type);
    formData.append('content', this.selectedEvent.content);
    formData.append('language_id', this.selectedEvent.language_id);
    formData.append('menu_name', this.selectedEvent.menu_name);
    formData.append('page_order', this.selectedEvent.page_order);
    formData.append('menu_position', this.selectedEvent.menu_position);
    formData.append('welcomedescription', this.selectedEvent.welcomedescription);
    formData.append('menu_url', this.selectedEvent.menu_url);
    formData.append('menu_link', this.selectedEvent.menu_link);
    formData.append('approve_status', this.selectedEvent.approve_status);
    formData.append('menu_description', this.selectedEvent.menu_description);
    formData.append('menu_child_id', this.selectedEvent.menu_child_id);
    if (this.fileToUploadDoc) {
      formData.append('doc_upload', this.fileToUploadDoc, this.fileToUploadDoc.name);
    }
    if (this.fileToUploadBanner) {
      formData.append('banner_img', this.fileToUploadBanner, this.fileToUploadBanner.name);
    }
    if (this.fileToUploadImg) {
      formData.append('img_upload', this.fileToUploadImg, this.fileToUploadImg.name);
    }
    if (this.userId) {
      formData.append('created_by', this.userId.toString()); // Convert to string
    }

    this.MenuService.updateEvent(this.selectedEvent.id, formData).subscribe(
      (event: HttpEvent<any>) => {
          this.loadList(this.lang_code); // Refresh the list of events
          this.closeEditModal(); // Close the modal or form
      },
      error => {
        console.error('Error saving event', error);
      }
    );
  }

  deleteEvent(id: number): void {
    if (confirm('Are you sure you want to delete this event?')) {
      this.MenuService.deleteEvent(id).subscribe(() => {
        this.events = this.events.filter(event => event.id !== id);
      });
    }
  }

  openEditModal(): void {
    const modalElement = document.getElementById('editEventModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  }

  closeEditModal(): void {
    const modalElement = document.getElementById('editEventModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) {
        modal.hide();
      }
    }
  }

  closeAddModal(): void {
    const modalElement = document.getElementById('addEventModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) {
        modal.hide();
      }
    }
  }

  onFileChange(event: any, type: string): void {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      const validTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Adjust types as needed
      if (!validTypes.includes(file.type)) {
        this.selectedFileError = 'Invalid file type. Only JPG, PNG, and PDF are allowed.';
        return;
      }
      if (file.size > 5000000) {
        this.selectedFileError = 'File size exceeds 5MB limit.';
        return;
      }
      switch (type) {
        case 'doc_upload':
          this.fileToUploadDoc = file;
          break;
        case 'banner_img':
          this.fileToUploadBanner = file;
          break;
        case 'img_upload':
          this.fileToUploadImg = file;
          break;
      }

      this.selectedFileError = null; // Clear any previous error
    }
  }
  oneditFileChange(event: any) {
    const file = event.target.files[0];
    if (file) {
      // Optionally validate file type and size
      if (file.size > 5000000) { // Example: limit to 5MB
        this.selectedFileError = 'File size exceeds 5MB limit.';
      } else {
        this.selectedFile = file;
        this.selectedFileError = '';
        this.fileToUpload = event.target.files[0];
      }
    }
  }
  loadUserId(): void {
    const userData = localStorage.getItem('user'); // Retrieve user data from localStorage
    if (userData) {
      const user = JSON.parse(userData); // Parse the JSON string back to an object
      this.userId = user.id; // Assign the user ID
      //console.log('User ID:', this.userId); // Log the user ID for verification
    } else {
      console.warn('No user data found in localStorage');
    }
  }
  getSanitizedHtml(html: string): SafeHtml {
    //console.log(html);
    if (html === 'null' || !html) {
      return  this['sanitizer'].bypassSecurityTrustHtml('Important Notification :');
    }
    return this['sanitizer'].bypassSecurityTrustHtml(html);
  }


  // Method to handle changes in menu type
  handleMenuType(id: any) { 
    this.selectedMenuType = id; // Update the selected menu type
    //console.log(this.selectedMenuType);
    //this.selectedEvent.menu_type='';
    this.formatEventDates();
  }
}
