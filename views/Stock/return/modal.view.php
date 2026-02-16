<div class="modal fade" id="addCustomer" role="dialog">
   <div class="modal-dialog">
     <div class="modal-content">
       <form method="post" id="product_action" class="form-horizontal" action="/AIS/purchase">
         <!-- Modal Header -->
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">
             <span aria-hidden="true">&times;</span>
             <span class="sr-only"> Close</span>
           </button>
           <h4 class="modal-title" id="myModalLabel">Add Supplier</h4>
         </div>
         <!-- Modal Body -->
         <div class="modal-body">
           <p id="statusMsg"></p><input type="hidden" name="mcustomer_id" id="mcustomer_id" value="0">
           <div class="form-group row">
             <label class="col-sm-2 col-form-label" for="name">Name</label>
             <div class="col-sm-10">
               <input type="text" placeholder="Name" class="form-control margin-bottom" id="mcustomer_name" name="name" required>
             </div>
           </div>
           <div class="form-group row">
             <label class="col-sm-2 col-form-label" for="phone"> Phone</label>
             <div class="col-sm-10">
               <input type="text" placeholder="Phone" class="form-control margin-bottom" name="phone" id="mcustomer_phone">
             </div>
           </div>
           <div class="form-group row">
             <label class="col-sm-2 col-form-label" for="email">Email</label>
             <div class="col-sm-10">
               <input type="email" placeholder="Email" class="form-control margin-bottom crequired" name="email" id="mcustomer_email">
             </div>
           </div>
           <div class="form-group row">
             <label class="col-sm-2 col-form-label" for="address"> Address</label>
             <div class="col-sm-10">
               <input type="text" placeholder="Address" class="form-control margin-bottom " name="address" id="mcustomer_address1">
             </div>
           </div>
           <div class="form-group row">
             <div class="col-sm-4">
               <input type="text" placeholder="City" class="form-control margin-bottom" name="city" id="mcustomer_city">
             </div>
             <div class="col-sm-4">
               <input type="text" placeholder="Region" class="form-control margin-bottom" name="region">
             </div>
             <div class="col-sm-4">
               <input type="text" placeholder="Country" class="form-control margin-bottom" name="country" id="mcustomer_country">
             </div>
           </div>
           <div class="form-group row">
             <div class="col-sm-6">
               <input type="text" placeholder="PostBox" class="form-control margin-bottom" name="postbox">
             </div>
             <div class="col-sm-6">
               <input type="text" placeholder="TAX ID" class="form-control margin-bottom" name="taxid" id="tax_id">
             </div>
           </div>
         </div>
         <!-- Modal Footer -->
         <div class="modal-footer">
           <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
           <button type="submit"  class="btn btn-primary submitBtn" >Add Supplier</button>
          
         </div>
       </form>
     </div>
   </div>
 </div>