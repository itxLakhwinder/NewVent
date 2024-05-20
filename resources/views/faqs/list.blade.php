@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> FAQs ({{count($faqs)}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
  <button type="button" data-toggle="modal" data-target="#addQues" class="btn btn-primary">Add</button>
  
  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Question</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        <td>@{{item.question}}</td>
        <td>@{{timeFormat(item.created_at)}}</td>
         <td>
          <a v-if="item.status == '1'" v-bind:href="'/faq/enable/' + item.id" onclick="return confirm('Are you sure?');">Enable</a>
          <a v-if="item.status != '1'" v-bind:href="'/faq/disable/' + item.id" onclick="return confirm('Are you sure?');">Disable</a>
        </td>
        <td>
          <a href="javascript:void(0)" class="" @click="editQues(item)">Edit</a> |
          <a href="#" v-bind:href="'/faq/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addQues" tabindex="-1" role="dialog" aria-labelledby="addQuesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addQuesLabel">ADD QUESTION</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('faqs.add')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Question:</label>
              <input type="text" class="form-control" id="question" name="question" required>
            </div>            
            <div class="form-group">
              <label for="email">Answer:</label>
              <div class="input_fields_container">
                <div>
                  <textarea name="options[]" required class="form-control" id="editor1"></textarea>
                  <!-- <input  type="text" > -->
                  <button type="button" class="btn btn-sm btn-primary add_more_button">Add More Fields</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" type="submit"  class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="updateQues" tabindex="-1" role="dialog" aria-labelledby="updateQuesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addResourceLabel">UPDATE QUESTION</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('faqs.update')}}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Name:</label>
              <input type="text" class="form-control" id="question" name="question" :value="item.question" required>
              <input type="hidden" name="id" v-model="item.id">
            </div>        
            <div class="form-group">
              <label for="email">Answer:</label>
              <div class="input_fields_container2">
                <div v-if="item.options.length" v-for="(n, index) in item.options">
                  <!-- <input type="text" name="options[]" required v-bind:id="'edit'+index"  :value="n" class="form-control answer"> -->
                  <textarea name="options[]" required class="form-control" v-bind:id="'edit'+index" :value="n">${n}</textarea>
                  <a href="#" class="remove_field2" style="margin-left:10px;">Remove</a>
                </div>
                <div>
                  <!-- <input type="text" name="options[]" required> -->
                  <button type="button"  class="btn btn-sm btn-primary add_more_button2">Add More Fields</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" type="submit"  class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@section('scripts')
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">
  const vApp = new Vue({
        el: '#vueEl',
        data: {            
            item: {},           
            items: <?= json_encode($faqs) ?>
        },
        mounted() {
          $('#dataTable').DataTable();
          var max_fields_limit      = 10; 
          var x = 1;
          $('.add_more_button').click(function(e){ 
              e.preventDefault();
              if(x < max_fields_limit){ 
                  x++; 
                  $('.input_fields_container').append('<div><textarea id="editor'+x+'" name="options[]" required class="form-control answer"></textarea><a href="#" class="remove_field" style="margin-left:10px;">Remove</a></div>'); 
              }

              CKEDITOR.replace( 'editor'+x );
          });  
          $('.input_fields_container').on("click",".remove_field", function(e){ //user click on remove text links
              e.preventDefault(); $(this).parent('div').remove(); x--;
          })


          var max_fields_limit2      = 10; 
          var x2 = 1;
          $('.add_more_button2').click(function(e){ 
              e.preventDefault();
              if(x2 < max_fields_limit2){ 
                  x2++; 
                  $('.input_fields_container2').append('<div><textarea id="editor22'+x2+'"  name="options[]" required class="form-control answer"></textarea><a href="#" class="remove_field2" style="margin-left:10px;">Remove</a></div>'); 
              }
              CKEDITOR.replace( 'editor22'+x2 );
          });  
          $('.input_fields_container2').on("click",".remove_field2", function(e){ //user click on remove text links
              e.preventDefault(); $(this).parent('div').remove(); x2--;
          })

          CKEDITOR.replace( 'editor1' );
         
        },
        methods: {
          editQues: function(item) {
            this.item = item;    
            $('#updateQues').modal('show');           
            setTimeout(function() {
              for(let key in item.options) {
                let dx = 'edit' + key;
                CKEDITOR.replace( dx );
                CKEDITOR.instances[dx].setData(item.options[key]);
              }    
            } , 500)
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          }
        }
    });
</script>
@endsection
@endsection