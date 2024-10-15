import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms'; // Import ReactiveFormsModule

@Component({
  selector: 'app-add',
  standalone: true,
  imports: [CommonModule,ReactiveFormsModule],
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.css'] // Corrected to styleUrls (array)
})
export class AddComponent implements OnInit {
  airportForm!: FormGroup; // Use the non-null assertion operator

  constructor(private fb: FormBuilder) {}

  ngOnInit(): void {
    // Initialize airportForm in ngOnInit
    this.airportForm = this.fb.group({
      airport_orders: ['', Validators.required],
      region_name: ['', Validators.required],
      sr_no: ['', Validators.required],
      airport_name: ['', Validators.required],
      entity_name: ['', Validators.required],
      address: ['', Validators.required],
      mobile_no: ['', Validators.required],
      phone_no: ['', Validators.required],
      unique_reference_number: ['', Validators.required],
      approved_status_clearance: ['', Validators.required],
      date_of_approval_clearance: ['', Validators.required],
      approved_status_programme: ['', Validators.required],
      date_of_approval_programme: ['', Validators.required],
      valid_till: ['', Validators.required]
    });
  }

  onSubmit() {
    if (this.airportForm.valid) {
      const newAirport = this.airportForm.value;
      // Save the new airport data or perform some other actions
      console.log(newAirport);
    }
  }
}
