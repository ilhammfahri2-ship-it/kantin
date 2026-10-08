@php
    // Partikel Debu Alami & Kilau Cahaya Hutan (Organic Komorebi & Nature Canopy)
    $natureMotes = [
        ['x' => 6,  'y' => 15, 'size' => 3.5, 'duration' => 6.2, 'delay' => 0.2, 'opacity' => 0.45],
        ['x' => 14, 'y' => 32, 'size' => 2.5, 'duration' => 7.5, 'delay' => 1.8, 'opacity' => 0.50],
        ['x' => 22, 'y' => 18, 'size' => 4.0, 'duration' => 8.1, 'delay' => 0.7, 'opacity' => 0.40],
        ['x' => 31, 'y' => 45, 'size' => 2.8, 'duration' => 6.8, 'delay' => 2.5, 'opacity' => 0.55],
        ['x' => 40, 'y' => 20, 'size' => 4.5, 'duration' => 9.2, 'delay' => 1.2, 'opacity' => 0.42],
        ['x' => 50, 'y' => 36, 'size' => 3.0, 'duration' => 7.0, 'delay' => 3.1, 'opacity' => 0.48],
        ['x' => 59, 'y' => 14, 'size' => 3.8, 'duration' => 8.4, 'delay' => 0.5, 'opacity' => 0.52],
        ['x' => 68, 'y' => 42, 'size' => 2.6, 'duration' => 7.9, 'delay' => 2.0, 'opacity' => 0.45],
        ['x' => 77, 'y' => 22, 'size' => 4.2, 'duration' => 8.6, 'delay' => 1.5, 'opacity' => 0.50],
        ['x' => 86, 'y' => 30, 'size' => 3.2, 'duration' => 7.2, 'delay' => 2.8, 'opacity' => 0.55],
        ['x' => 94, 'y' => 16, 'size' => 2.5, 'duration' => 6.5, 'delay' => 0.9, 'opacity' => 0.40],
        ['x' => 12, 'y' => 72, 'size' => 3.2, 'duration' => 8.0, 'delay' => 2.1, 'opacity' => 0.42],
        ['x' => 27, 'y' => 84, 'size' => 4.0, 'duration' => 9.5, 'delay' => 3.4, 'opacity' => 0.48],
        ['x' => 46, 'y' => 68, 'size' => 2.8, 'duration' => 7.7, 'delay' => 1.0, 'opacity' => 0.50],
        ['x' => 64, 'y' => 80, 'size' => 3.5, 'duration' => 8.8, 'delay' => 2.6, 'opacity' => 0.44],
        ['x' => 82, 'y' => 70, 'size' => 3.0, 'duration' => 7.4, 'delay' => 1.7, 'opacity' => 0.46],
    ];

    // Daun Organik Melayang Lembut (Drifting Forest Leaves)
    $fallingLeaves = [
        ['top' => '-25px', 'left' => '12%', 'duration' => 18, 'delay' => 0,   'size' => 14, 'rotation' => 25],
        ['top' => '-35px', 'left' => '34%', 'duration' => 22, 'delay' => 5,   'size' => 12, 'rotation' => -40],
        ['top' => '-20px', 'left' => '58%', 'duration' => 19, 'delay' => 2.5, 'size' => 16, 'rotation' => 60],
        ['top' => '-40px', 'left' => '82%', 'duration' => 24, 'delay' => 8,   'size' => 13, 'rotation' => -20],
        ['top' => '-30px', 'left' => '94%', 'duration' => 20, 'delay' => 12,  'size' => 15, 'rotation' => 45],
    ];
@endphp

{{-- Wadah Efek Alam Komorebi & Serat Pohon Alami --}}
<div class="meteor-shower-wrapper organic-canopy-wrapper" aria-hidden="true">
    {{-- Cahaya Sinar Matahari Komorebi Menembus Tajuk Pohon --}}
    <div class="canopy-sunbeam-glow"></div>

    {{-- Partikel Debu Emas & Spora Kayu Melayang Halus --}}
    <div class="nature-motes-container">
        @foreach($natureMotes as $mote)
            <span class="nature-pollen-mote" style="
                left: {{ $mote['x'] }}%;
                top: {{ $mote['y'] }}%;
                width: {{ $mote['size'] }}px;
                height: {{ $mote['size'] }}px;
                --mote-duration: {{ $mote['duration'] }}s;
                --mote-delay: {{ $mote['delay'] }}s;
                --mote-opacity: {{ $mote['opacity'] }};
            "></span>
        @endforeach
    </div>

    {{-- Siluet Daun Organik Melayang Anggun --}}
    <div class="drifting-leaves-container">
        @foreach($fallingLeaves as $leaf)
            <span class="drifting-leaf" style="
                top: {{ $leaf['top'] }};
                left: {{ $leaf['left'] }};
                width: {{ $leaf['size'] }}px;
                height: {{ $leaf['size'] }}px;
                --leaf-duration: {{ $leaf['duration'] }}s;
                --leaf-delay: {{ $leaf['delay'] }}s;
                --leaf-rotation: {{ $leaf['rotation'] }}deg;
            ">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full opacity-35 dark:opacity-20 text-emerald-700 dark:text-emerald-400">
                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5c0 3.5 3.5 5.5 6 5.5 5 0 9-5 9-11z"/>
                </svg>
            </span>
        @endforeach
    </div>
</div>
