 @extends('layouts.master')
@section('content')
@section('title', 'Manage Module')
 <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label> Position:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="mod_order_id" class="input_class form-control @error('mod_order_id') is-invalid @enderror" id="mod_order_id" autocomplete="off">
                                        <option value=""> Select </option>
                                            <?php
                                             for($i=0; $i<= 50; $i++ ) {
                                                ?>
                                                <option value="<?php echo $i; ?>" <?php if((!empty($data->mod_order_id)?$data->mod_order_id:old('mod_order_id'))==$i) echo "selected"; ?>><?php echo $i; ?></option>
                                            <?php  }?>
                                    </select>
                                    @error('mod_order_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endsection