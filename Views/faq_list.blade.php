@extends($sc_templatePath.'.layout')

@section('block_main')
<section class="section section-xl bg-default">
  <div class="container">
    <div class="row row-30">
      @if ($itemsList->count())
          @foreach ($itemsList as $faqCategory)
          <div class="col-sm-6 col-lg-4">
              <!-- Post Classic-->
              <article class="post post-classic box-md"><a class="post-classic-figure" href="{{ $faqCategory->getUrl() }}">
                  <img src="{{ asset($faqCategory->getThumb()) }}" alt="" width="370" height="239"></a>
                <div class="post-classic-content">
                  <h5 class="post-classic-title"><a href="{{ $faqCategory->getUrl() }}">{{ $faqCategory->title }}</a></h5>
                  <p class="post-classic-text">
                      {{ $faqCategory->description }}
                  </p>
                </div>
              </article>
            </div>
          @endforeach

          <div class="pagination-wrap">
              <!-- Bootstrap Pagination-->
              <nav aria-label="Page navigation">
                  {{ $itemsList->links() }}
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
