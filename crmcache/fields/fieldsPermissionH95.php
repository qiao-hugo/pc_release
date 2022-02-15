<?php
$fieldsPermission=array('readonly'=>array(66,68,69,71,72,73,75,76,77,78,79,80,82,83,84,85,86,87,90,91,92,96,97,99,100,101,102,103,105,109,1610,1611,1612,1,2,3,4,5,8,9,14,17,20,21,22,23,26,28,32,34,36,152,177,179,197,1602,1606,2223,2224,2225,2226,2247,2252,2332,2476,2517,2540,18,2655,3121,3547,173,174,178,183,187,190,191,192,193,196,200,203,1716,2204,2205,2206,2207,2208,2209,2263,2342,2360,2370,2374,2513,3426,3427,3428,3429,3430,3431,3432,3433,3434,3435,3436,3437,3438,3439,3440,3441,3442,3443,3444,3445,3446,3447,3448,3449,3450,3451,3452,3453,3454,3455,3456,3457,3458,3459,3460,3461,3462,3463,3464,3465,3466,3467,3468,3469,3470,3471,3472,3618,388,389,390,391,397,400,408,409,410,411,412,427,1656,1658,2255,2288,2289,2339,2361,2389,2678,2714,2699,2700,2701,524,525,526,529,537,538,539,541,542,683,1921,2262,2324,2326,2327,2345,2346,2390,2391,2393,2427,2479,2562,2609,2611,2653,2665,2667,2679,2692,2709,2710,2711,2712,2858,2863,2867,3352,3598,3599,3608,3609,1637,1638,1639,1642,1643,1644,1645,1646,1712,1845,1739,1740,1741,1742,1743,1744,1746,1748,1749,1753,1754,1755,1756,1834,1922,2285,2294,2295,2328,2670,2672,2674,2675,2789,2861,3610,3686,3687,1904,1905,1906,1907,1908,1909,1910,1911,1914,1915,1916,1917,1919,2358,2510,1872,1873,1881,1882,1884,1885,1887,2359,2407,2429,2470,2471,2478,3678,2362,2363,2364,2365,2366,2367,2368,2369,2376,2607,2704,3122,3043,3044,3045,3046,3047,3048,3049,3050,3051,3052,3053,3054,3055,3056,3057,3058,3059,3060,3061,3062,3063,3064,3003,3004,3005,3006,3007,3008,3009,3010,3011,3012,3115,3013,3014,3015,3016,3018,3019,3020,3021,3068,3069,3070,3071,3072,3073,3074,3075,3076,3077,3078,3079,3080,3081,3082,3083,3084,3085,3086,3087,3088,3089,3090,3091,3092,3093,3094,3095,3096,3097,3098,3099,3155,3679,3779,3680,3100,3101,3102,3103,3104,3105,3106,3107,3108,3109,3110,3111,3112,3113,3114,3496,3566,3567,3568,3569,3619,3628,3677,3117,3118,3119,3120,3353,3359,3362,3363,3367,3369,3370,3372,3373,3374,3375,3376,3390,3391,3392,3393,3394,3395,3396,3397,3398,3399,3400,3403,3405,3407,3408,3409,3410,3411,3412,3413,3414,3415,3416,3417,3418,3420,3422,3423,3424,3560,3611,3612,3615,3616,3617,3676,3582,3583,3584,3585,3586,3587,3588,3589,3590,3591,3592,3593,3594,3595,3596,3597,3629,3630,3631,3632,3633,3634,3635,3636,3637,3638,3639,3640,3641,3642,3643,3644,3681,3682,3683,3684,3685,3734,3735,3736,3737,3738,3739,3740,3741,3742,3743,3744,3745,3746,3747,3748,3750,3751,3752,3753,3754,3755,3756,3758,3760,3762,3763,3764,3765,3766,3767,3769,3771,3773,3774,3775,3776,3777,3778,40,41,42,43,44,46,47,48,49,51,54,56,57,58,65,110,111,113,114,115,117,118,119,120,122,123,124,125,175,185,189,313,314,315,316,324,329,330,331,332,333,348,438,447,451,453,454,456,472,473,634,678,700,1661,1677,1678,1679,1680,1682,1684,1685,1686,1687,1688,1689,1690,1692,1696,1697,1698,1789,1790,1832,1835,1836,1837,1838,1839,1840,1841,1842,1843,1850,1851,1852,1853,1854,1855,1856,1857,1858,1859,1860,1861,1862,1863,1864,2191,2192,2193,2194,2195,2196,2197,2198,2199,2200,2202,2211,2214,2219,2220,2222,2245,2246,2251,2258,2259,2260,2272,2274,2275,2276,2277,2278,2280,2281,2282,2284,2287,2296,2299,2300,2301,2335,2371,2392,2402,2042,2426,2409,2410,2411,2412,2413,2414,2415,2428,2499,2500,2526,2527,2529,2532,2533,2534,2535,2536,2537,2538,2539,2541,2542,2544,2613,2615,2617,2619,2621,2623,2625,2627,2629,2631,2633,2635,2637,2639,2641,2651,2659,2661,2663,2669,2676,2677,2680,2691,2693,2694,2695,2696,2698,2706,2707,2708,2713,2733,2788,2794,2795,2796,2797,2798,2799,2800,2801,2802,2803,2804,2805,2806,2807,2808,2809,2810,2811,2812,2813,2814,2815,2816,2817,2818,2819,2820,2821,2822,2823,2824,2825,2826,2827,2828,2829,2830,2831,2832,2833,2859,2860,2864,2865,2989,3341,3342,3343,3344,3345,3346,3347,3348,3425),'noreadonly'=>array(66,68,69,71,72,73,75,76,77,78,79,80,82,83,84,85,86,87,92,96,97,99,100,101,102,103,105,109,1610,1611,1612,1,2,3,4,5,8,9,14,17,20,23,26,28,32,34,36,152,179,197,1602,1606,2223,2224,2225,2226,2247,2252,2332,2476,2517,2540,18,3547,173,174,178,183,187,192,193,196,200,203,1716,2204,2205,2206,2207,2208,2209,2263,2342,2360,2370,2374,2513,3426,3427,3428,3429,3430,3431,3432,3435,3436,3437,3438,3439,3440,3442,3443,3444,3445,3446,3447,3448,3449,3450,3451,3452,3453,3454,3455,3456,3457,3458,3460,3461,3463,3464,3466,3467,3468,3469,3470,3471,3472,3618,388,389,390,391,397,400,408,409,412,427,1656,1658,2255,2288,2289,2339,2361,2389,2678,2714,2699,2700,2701,524,529,537,538,539,2262,2324,2327,2345,2390,2391,2479,2867,3352,1637,1638,1639,1642,1645,1646,1712,1845,1739,1740,1741,1742,1743,1744,1746,1748,1749,1756,1834,1922,2285,2294,2295,2328,2861,3686,3687,1904,1905,1907,1911,1914,1915,1916,2358,2510,1872,1873,1881,1882,1884,1885,1887,2359,2407,2429,2470,2471,2478,3678,2362,2363,2365,2367,2369,2376,2607,2704,3122,3046,3047,3048,3049,3050,3051,3052,3053,3054,3055,3056,3057,3058,3059,3062,3063,3064,3003,3004,3005,3006,3007,3008,3011,3012,3115,3014,3015,3018,3070,3071,3072,3073,3078,3079,3080,3081,3082,3083,3084,3085,3086,3087,3088,3089,3090,3091,3092,3093,3094,3095,3096,3097,3098,3099,3155,3679,3779,3680,3100,3101,3103,3105,3109,3111,3112,3113,3114,3496,3566,3567,3568,3569,3677,3117,3118,3119,3120,3353,3359,3362,3367,3370,3372,3375,3376,3390,3391,3392,3393,3394,3395,3396,3397,3398,3399,3400,3405,3407,3408,3409,3410,3411,3412,3413,3414,3415,3416,3417,3418,3420,3422,3423,3424,3611,3612,3615,3616,3617,3676,3582,3586,3587,3588,3589,3590,3591,3592,3593,3594,3596,3629,3633,3634,3635,3636,3637,3638,3639,3640,3641,3643,3681,3682,3683,3734,3735,3736,3737,3738,3739,3740,3741,3742,3743,3746,3747,3748,3750,3751,3752,3753,3755,3756,3758,3760,3762,3763,3764,3765,3766,3767,3769,3771,3773,3774,3775,3778,40,41,42,43,44,46,47,48,49,51,54,58,65,110,111,113,114,115,117,118,119,120,124,125,175,185,189,313,314,315,316,324,329,330,333,348,438,447,451,453,634,678,700,1661,1677,1678,1679,1680,1682,1684,1689,1690,1692,1696,1697,1698,1789,1790,1832,1835,1837,1838,1839,1840,1842,1843,1850,1851,1852,1853,1856,1857,1858,1859,1860,1861,1862,1863,1864,2191,2192,2193,2194,2199,2200,2202,2211,2214,2219,2220,2222,2245,2246,2251,2258,2259,2272,2274,2275,2276,2277,2278,2280,2281,2284,2287,2296,2299,2300,2301,2335,2371,2392,2402,2042,2426,2428,2499,2500,2529,2532,2533,2534,2535,2538,2539,2541,2544,2613,2615,2617,2619,2621,2623,2625,2627,2651,2661,2663,2669,2676,2677,2680,2691,2695,2696,2698,2706,2707,2708,2713,2788,2794,2795,2796,2797,2798,2800,2801,2802,2803,2804,2805,2806,2807,2808,2809,2810,2811,2812,2814,2815,2816,2817,2819,2820,2821,2822,2823,2824,2825,2826,2827,2828,2829,2830,2831,2832,2833,2859,2860,2864,2865,3341,3342,3343,3344,3345,3346,3347,3348));
?>