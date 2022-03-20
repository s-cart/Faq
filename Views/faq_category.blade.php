@extends($sc_templatePath.'.layout')

@section('block_main')
<section class="section section-xl bg-default">
  <div class="container">
    <div class="row row-30">
      @if ($entries->count())
          @foreach ($entries as $entryDetail)
          <div class="col-sm-6 col-lg-4">
              <!-- Post Classic-->
              <article class="post post-classic box-md"><a class="post-classic-figure" href="{{ $entryDetail->getUrl() }}">
                  <img src="{{ asset($entryDetail->getThumb()) }}" alt="" width="370" height="239"></a>
                <div class="post-classic-content">
                  <h5 class="post-classic-title"><a href="{{ $entryDetail->getUrl() }}">{{ $entryDetail->question }}</a></h5>
                </div>
              </article>
            </div>
          @endforeach

          <div class="pagination-wrap">
              <!-- Bootstrap Pagination-->
              <nav aria-label="Page navigation">
                  {{ $entries->links() }}
              </nav>
            </div>

      @else
          {!! sc_language_render('front.data_not_found') !!}
      @endif
    </div>

  </div>
</section>
@endsection

{{-- breadcrumb --}}
@section('breadcrumb')
<section class="breadcrumbs-custom">
    <div class="breadcrumbs-custom-footer">
        <div class="container">
          <ul class="breadcrumbs-custom-path">
            <li><a href="{{ sc_route('home') }}">{{ sc_language_render('front.home') }}</a></li>
            <li class="active">{{ $title ?? '' }}</li>
          </ul>
        </div>
    </div>
</section>
@endsection
{{-- //breadcrumb --}}
