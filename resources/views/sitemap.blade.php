@extends('shared.master')

@section('meta_title', 'Sitemap | PT. Sidoagung Farm')
@section('meta_description', 'Daftar halaman penting website PT. Sidoagung Farm untuk memudahkan navigasi pengunjung dan mesin pencari.')
@section('canonical_url', route('sitemap'))

@section('content')
    <x-banner-summary mode="about"></x-banner-summary>

    <section class="space-ptb sitemap-section">
        <div class="container">
            
            @foreach (collect($sections)->chunk(3) as $chunk)
                <div class="row">
                    @foreach ($chunk as $section)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="sitemap-card">
                                <h4 class="sitemap-title">{{ $section['title'] }}</h4>
                                <ul class="sitemap-list">
                                    @foreach ($section['links'] as $link)
                                        <li>
                                            <a href="{{ $link['url'] }}"
                                                @if (! empty($link['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                                {{ $link['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('css')
    <style>
        .sitemap-section {
            background: linear-gradient(135deg, rgba(0, 166, 81, 0.05) 0%, #ffffff 40%, rgba(237, 29, 36, 0.05) 100%);
            position: relative;
            overflow: hidden;
        }

        .sitemap-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(rgba(0, 166, 81, 0.14) 1px, transparent 1px),
                radial-gradient(rgba(237, 29, 36, 0.1) 1px, transparent 1px);
            background-size: 34px 34px, 52px 52px;
            background-position: 0 0, 26px 26px;
            opacity: 0.26;
            pointer-events: none;
        }

        .sitemap-section::after {
            content: "";
            position: absolute;
            width: 520px;
            height: 520px;
            right: -220px;
            top: -220px;
            background: radial-gradient(circle, rgba(0, 166, 81, 0.2) 0%, rgba(0, 166, 81, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .sitemap-section .container {
            position: relative;
            z-index: 1;
        }

        .sitemap-card {
            background: #ffffff;
            border: 1px solid rgba(0, 166, 81, 0.15);
            border-radius: 14px;
            padding: 18px 18px 14px;
            box-shadow: 0 8px 20px rgba(24, 24, 24, 0.06);
            min-height: 100%;
        }

        .sitemap-title {
            font-weight: 700;
            margin-bottom: 12px;
            color: #00a651;
        }

        .sitemap-list {
            padding-left: 18px;
            margin-bottom: 0;
        }

        .sitemap-list li {
            margin-bottom: 8px;
            color: #ed1d24;
        }

        .sitemap-list a {
            color: #242424;
        }

        .sitemap-list a:hover {
            color: #ed1d24;
        }
    </style>
@endsection
