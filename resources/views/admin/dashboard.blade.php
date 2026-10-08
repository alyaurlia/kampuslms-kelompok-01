@php

    $persentaseDinilaiTampilan = number_format(
        $persentaseDinilai,
        2,
        ',',
        '.'
    );

    /*
    |--------------------------------------------------------------------------
    | OVERVIEW AKTIVITAS
    |--------------------------------------------------------------------------
    */

    $aktivitasHariIni = \App\Models\Submission::whereDate(
        'submitted_at',
        today()
    )->count();

    $aktivitasMingguIni = \App\Models\Submission::whereBetween(
        'submitted_at',
        [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]
    )->count();

    $aktivitasBulanIni = \App\Models\Submission::whereMonth(
        'submitted_at',
        now()->month
    )
        ->whereYear(
            'submitted_at',
            now()->year
        )
        ->count();

    $tanggalAktivitas = \App\Models\Submission::whereNotNull('submitted_at')
        ->selectRaw('DATE(submitted_at) as tanggal, COUNT(*) as jumlah')
        ->groupBy('tanggal')
        ->orderByDesc('tanggal')
        ->take(7)
        ->get();

@endphp


<x-layout title="Dashboard Admin" role="admin">

    <style>

        /* =========================================================
           PEMANTAUAN PENGUMPULAN
           ========================================================= */

        .admin-submission-panel {
            position: relative;
            margin-top: 22px;
            padding: 30px 32px 28px;
            background: #ffffff;
            border: 1px solid #eadfd9;
            border-radius: 24px;
            overflow: hidden;
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .admin-submission-panel:hover {
            transform: translateY(-3px);
            border-color: #dfc9c2;
            box-shadow: 0 18px 40px rgba(86, 45, 35, 0.08);
        }

        .admin-submission-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: #9f1239;
        }

        .admin-submission-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-submission-eyebrow,
        .admin-activity-eyebrow,
        .admin-quick-eyebrow,
        .admin-course-eyebrow,
        .admin-overview-eyebrow {
            margin: 0 0 7px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.16em;
        }

        .admin-submission-eyebrow,
        .admin-activity-eyebrow,
        .admin-course-eyebrow {
            color: #9f1239;
        }

        .admin-quick-eyebrow {
            color: #7c3aed;
        }

        .admin-overview-eyebrow {
            color: #9f1239;
        }

        .admin-submission-title,
        .admin-activity-title,
        .admin-quick-title,
        .admin-course-title,
        .admin-overview-title {
            margin: 0;
            font-size: 25px;
            line-height: 1.15;
            color: #172033;
        }

        .admin-submission-description,
        .admin-activity-description,
        .admin-quick-description,
        .admin-course-description,
        .admin-overview-description {
            max-width: 600px;
            margin: 8px 0 0;
            font-size: 13px;
            line-height: 1.6;
            color: #7c6f69;
        }

        .admin-submission-total {
            min-width: 145px;
            padding: 13px 16px;
            text-align: right;
            background: #fff7f8;
            border: 1px solid #f0d9de;
            border-radius: 15px;
            cursor: pointer;
            transition:
                transform 0.22s ease,
                background-color 0.22s ease,
                border-color 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-submission-total:hover {
            transform: translateY(-4px);
            background: #fbecef;
            border-color: #e7b9c5;
            box-shadow: 0 10px 24px rgba(159, 18, 57, 0.10);
        }

        .admin-submission-total:active {
            transform: translateY(-1px) scale(0.98);
        }

        .admin-submission-total span {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #9a6e77;
        }

        .admin-submission-total strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #9f1239;
            transition:
                transform 0.22s ease,
                color 0.22s ease;
        }

        .admin-submission-total:hover strong {
            transform: scale(1.06);
            color: #831337;
        }

        .admin-submission-body {
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 34px;
            padding-top: 27px;
        }

        .admin-submission-progress {
            padding-right: 30px;
            cursor: pointer;
        }

        .admin-submission-progress__top {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 10px;
        }

        .admin-submission-progress__label {
            font-size: 13px;
            font-weight: 600;
            color: #3f3531;
        }

        .admin-submission-progress__percent {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #9f1239;
            transition:
                transform 0.22s ease,
                color 0.22s ease;
        }

        .admin-submission-progress:hover
        .admin-submission-progress__percent {
            transform: translateY(-3px) scale(1.05);
            color: #831337;
        }

        .admin-submission-progress__track {
            width: 100%;
            height: 11px;
            overflow: hidden;
            background: #f0e9e6;
            border-radius: 99px;
            transition:
                height 0.22s ease,
                background-color 0.22s ease;
        }

        .admin-submission-progress:hover
        .admin-submission-progress__track {
            height: 14px;
            background: #f5e3e7;
        }

        .admin-submission-progress__bar {
            height: 100%;
            background: linear-gradient(
                90deg,
                #9f1239,
                #c24168
            );
            border-radius: 99px;
            transition:
                filter 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-submission-progress:hover
        .admin-submission-progress__bar {
            filter: brightness(1.08);
            box-shadow: 0 0 12px rgba(159, 18, 57, 0.22);
        }

        .admin-submission-progress__caption {
            margin: 11px 0 0;
            font-size: 12px;
            line-height: 1.55;
            color: #958881;
        }

        .admin-submission-status {
            display: flex;
            flex-direction: column;
            border-left: 1px solid #eee4df;
        }

        .admin-submission-status__item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            min-height: 82px;
            padding: 0 0 0 24px;
            border-radius: 13px;
            cursor: pointer;
            transition:
                transform 0.22s ease,
                background-color 0.22s ease,
                padding-left 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-submission-status__item + .admin-submission-status__item {
            border-top: 1px solid #eee4df;
        }

        .admin-submission-status__item:hover {
            padding-left: 29px;
            background: #fffafa;
            transform: translateX(4px);
        }

        .admin-submission-status__item:active {
            transform: translateX(4px) scale(0.98);
        }

        .admin-submission-status__identity {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .admin-submission-status__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 11px;
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-submission-status__item:hover
        .admin-submission-status__icon {
            transform: rotate(-5deg) scale(1.08);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
        }

        .admin-submission-status__icon svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-submission-status__icon--done {
            color: #16804b;
            background: #e9f7ef;
        }

        .admin-submission-status__icon--pending {
            color: #a16207;
            background: #fff4d8;
        }

        .admin-submission-status__label {
            font-size: 12px;
            font-weight: 600;
            color: #675a55;
        }

        .admin-submission-status__count {
            min-width: 38px;
            text-align: right;
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
            color: #172033;
            transition:
                transform 0.22s ease,
                color 0.22s ease;
        }

        .admin-submission-status__item:hover
        .admin-submission-status__count {
            transform: translateX(-3px) scale(1.08);
            color: #9f1239;
        }

        .admin-submission-foot {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            padding-top: 17px;
            border-top: 1px solid #eee4df;
            font-size: 11px;
            line-height: 1.5;
            color: #958881;
        }

        .admin-submission-foot__mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex: 0 0 20px;
            border: 1px solid #d9ccc6;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            color: #9f1239;
            background: #fff8f9;
            transition:
                transform 0.2s ease,
                background-color 0.2s ease,
                border-color 0.2s ease;
        }

        .admin-submission-foot:hover
        .admin-submission-foot__mark {
            transform: rotate(8deg);
            background: #fbecef;
            border-color: #e7b9c5;
        }


        /* =========================================================
           AKTIVITAS TERBARU
           ========================================================= */

        .admin-activity-panel {
            position: relative;
            margin-top: 22px;
            padding: 30px 32px 28px;
            background: #ffffff;
            border: 1px solid #eadfd9;
            border-radius: 24px;
            overflow: hidden;
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .admin-activity-panel:hover {
            transform: translateY(-3px);
            border-color: #dfc9c2;
            box-shadow: 0 18px 40px rgba(86, 45, 35, 0.08);
        }

        .admin-activity-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: #c24168;
        }

        .admin-activity-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
            padding-bottom: 22px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-activity-list {
            display: flex;
            flex-direction: column;
        }

        .admin-activity-item {
            display: flex;
            align-items: center;
            gap: 16px;
            min-height: 76px;
            padding: 14px 4px;
            border-bottom: 1px solid #eee4df;
            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .admin-activity-item:last-child {
            border-bottom: 0;
        }

        .admin-activity-item:hover {
            background: #fffafa;
            transform: translateX(4px);
        }

        .admin-activity-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            color: #9f1239;
            background: #fbecef;
            border-radius: 12px;
        }

        .admin-activity-icon svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-activity-content {
            min-width: 0;
            flex: 1;
        }

        .admin-activity-content strong {
            display: block;
            margin-bottom: 3px;
            font-size: 13px;
            color: #29211e;
        }

        .admin-activity-content p {
            margin: 0;
            font-size: 12px;
            line-height: 1.45;
            color: #756863;
        }

        .admin-activity-time {
            flex: 0 0 auto;
            font-size: 11px;
            color: #9a8d87;
            white-space: nowrap;
        }

        .admin-activity-empty {
            padding: 32px 10px;
            text-align: center;
            font-size: 13px;
            color: #958881;
        }


        /* =========================================================
           QUICK ACTION
           ========================================================= */

        .admin-quick-panel {
            position: relative;
            margin-top: 22px;
            padding: 30px 32px 28px;
            background: #ffffff;
            border: 1px solid #eadfd9;
            border-radius: 24px;
            overflow: hidden;
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .admin-quick-panel:hover {
            transform: translateY(-3px);
            border-color: #dfc9c2;
            box-shadow: 0 18px 40px rgba(86, 45, 35, 0.08);
        }

        .admin-quick-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: #8b5cf6;
        }

        .admin-quick-header {
            padding-bottom: 22px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-quick-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            padding-top: 22px;
        }

        .admin-quick-action {
            display: flex;
            align-items: center;
            gap: 15px;
            min-height: 78px;
            padding: 16px 18px;
            color: inherit;
            text-decoration: none;
            background: #fffaf9;
            border: 1px solid #eee4df;
            border-radius: 16px;
            transition:
                transform 0.2s ease,
                border-color 0.2s ease,
                background-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .admin-quick-action:hover {
            transform: translateY(-3px);
            background: #ffffff;
            border-color: #d9c2ba;
            box-shadow: 0 10px 24px rgba(86, 45, 35, 0.08);
        }

        .admin-quick-action__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            color: #9f1239;
            background: #fbecef;
            border-radius: 12px;
        }

        .admin-quick-action__icon svg {
            width: 19px;
            height: 19px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-quick-action__content {
            min-width: 0;
            flex: 1;
        }

        .admin-quick-action__content strong {
            display: block;
            margin-bottom: 3px;
            font-size: 13px;
            color: #29211e;
        }

        .admin-quick-action__content span {
            display: block;
            font-size: 11px;
            line-height: 1.45;
            color: #81736d;
        }

        .admin-quick-action__arrow {
            font-size: 18px;
            color: #9f1239;
            transition: transform 0.2s ease;
        }

        .admin-quick-action:hover .admin-quick-action__arrow {
            transform: translateX(4px);
        }


        /* =========================================================
           RINGKASAN MATA KULIAH
           ========================================================= */

        .admin-course-panel {
            position: relative;
            margin-top: 22px;
            padding: 30px 32px 28px;
            background: #ffffff;
            border: 1px solid #eadfd9;
            border-radius: 24px;
            overflow: hidden;
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .admin-course-panel:hover {
            transform: translateY(-3px);
            border-color: #dfc9c2;
            box-shadow: 0 18px 40px rgba(86, 45, 35, 0.08);
        }

        .admin-course-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: #c24168;
        }

        .admin-course-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
            padding-bottom: 22px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-course-total {
            min-width: 130px;
            padding: 13px 16px;
            text-align: right;
            background: #fff7f8;
            border: 1px solid #f0d9de;
            border-radius: 15px;
            cursor: pointer;
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease,
                border-color 0.22s ease;
        }

        .admin-course-total:hover {
            transform: translateY(-4px);
            border-color: #e7b9c5;
            box-shadow: 0 10px 24px rgba(159, 18, 57, 0.10);
        }

        .admin-course-total span {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #9a6e77;
        }

        .admin-course-total strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
            color: #9f1239;
            transition: transform 0.22s ease;
        }

        .admin-course-total:hover strong {
            transform: scale(1.06);
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER KOLOM
        |--------------------------------------------------------------------------
        */

        .admin-course-columns {
            display: grid;
            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(180px, 1fr)
                90px
                150px;
            gap: 20px;
            align-items: center;
            margin-top: 20px;
            padding: 0 4px 11px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-course-columns span {
            font-size: 10px;
            font-weight: 800;
            line-height: 1.2;
            color: #9a8d87;
            text-transform: uppercase;
            letter-spacing: 0.09em;
        }

        .admin-course-list {
            display: flex;
            flex-direction: column;
        }

        .admin-course-item {
            display: grid;
            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(180px, 1fr)
                90px
                150px;
            align-items: center;
            gap: 20px;
            min-height: 82px;
            padding: 15px 4px;
            border-bottom: 1px solid #eee4df;
            transition:
                background-color 0.2s ease,
                transform 0.2s ease,
                padding-left 0.2s ease;
        }

        .admin-course-item:last-child {
            border-bottom: 0;
        }

        .admin-course-item:hover {
            background: #fffafa;
            transform: translateX(4px);
            padding-left: 9px;
        }

        .admin-course-name {
            min-width: 0;
        }

        .admin-course-code {
            display: inline-block;
            margin-bottom: 6px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0.07em;
            color: #9f1239;
            background: #fbecef;
            border: 1px solid #f0d9de;
            border-radius: 7px;
        }

        .admin-course-name strong {
            display: block;
            font-size: 14px;
            line-height: 1.4;
            font-weight: 700;
            color: #29211e;
        }

        .admin-course-lecturer {
            min-width: 0;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 600;
            color: #514641;
        }

        .admin-course-sks {
            font-size: 14px;
            line-height: 1.4;
            font-weight: 700;
            color: #514641;
        }

        .admin-course-students {
            font-size: 13px;
            line-height: 1.4;
            font-weight: 700;
            color: #514641;
        }

        .admin-course-empty {
            padding: 35px 10px;
            text-align: center;
            font-size: 13px;
            color: #958881;
        }


        /* =========================================================
           FITUR 7 — OVERVIEW AKTIVITAS
           ========================================================= */

        .admin-overview-panel {
            position: relative;
            margin-top: 22px;
            padding: 30px 32px 28px;
            background: #ffffff;
            border: 1px solid #eadfd9;
            border-radius: 24px;
            overflow: hidden;
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .admin-overview-panel:hover {
            transform: translateY(-3px);
            border-color: #dfc9c2;
            box-shadow: 0 18px 40px rgba(86, 45, 35, 0.08);
        }

        .admin-overview-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: #9f1239;
        }

        .admin-overview-header {
            padding-bottom: 22px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-overview-title {
            font-size: 26px;
        }

        .admin-overview-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            padding: 22px 0;
        }

        .admin-overview-card {
            position: relative;
            padding: 20px 21px;
            background: #fffaf9;
            border: 1px solid #f0e4df;
            border-radius: 16px;
            cursor: pointer;
            overflow: hidden;
            transition:
                transform 0.22s ease,
                background-color 0.22s ease,
                border-color 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-overview-card::after {
            content: "";
            position: absolute;
            right: -18px;
            bottom: -24px;
            width: 80px;
            height: 80px;
            background: #fbecef;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .admin-overview-card:hover {
            transform: translateY(-5px);
            background: #ffffff;
            border-color: #e7b9c5;
            box-shadow: 0 12px 25px rgba(159, 18, 57, 0.08);
        }

        .admin-overview-card:hover::after {
            transform: scale(1.35);
        }

        .admin-overview-card__label {
            position: relative;
            z-index: 1;
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: 700;
            color: #9a6e77;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .admin-overview-card__number {
            position: relative;
            z-index: 1;
            display: inline-block;
            margin-right: 5px;
            font-size: 32px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #9f1239;
            transition:
                transform 0.22s ease,
                color 0.22s ease;
        }

        .admin-overview-card:hover .admin-overview-card__number {
            transform: translateY(-3px) scale(1.07);
            color: #831337;
        }

        .admin-overview-card__caption {
            position: relative;
            z-index: 1;
            font-size: 12px;
            color: #81736d;
        }

        /*
        |--------------------------------------------------------------------------
        | GRID AKTIVITAS
        |--------------------------------------------------------------------------
        */

        .admin-overview-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(300px, 0.75fr);
            gap: 24px;
            padding-top: 5px;
        }

        .admin-overview-column {
            min-width: 0;
            padding: 20px 21px;
            background: #fffdfc;
            border: 1px solid #eee4df;
            border-radius: 18px;
        }

        .admin-overview-section-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 5px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee4df;
        }

        .admin-overview-section-title h3 {
            margin: 0;
            font-size: 16px;
            line-height: 1.3;
            color: #29211e;
        }

        .admin-overview-section-title p {
            margin: 4px 0 0;
            font-size: 11px;
            line-height: 1.4;
            color: #958881;
        }

        .admin-overview-section-title span {
            flex: 0 0 auto;
            padding: 5px 9px;
            font-size: 10px;
            font-weight: 700;
            color: #9f1239;
            background: #fbecef;
            border-radius: 7px;
        }

        .admin-overview-activity {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 76px;
            padding: 12px 3px;
            border-bottom: 1px solid #f0e9e6;
            cursor: pointer;
            transition:
                background-color 0.2s ease,
                transform 0.2s ease,
                padding-left 0.2s ease;
        }

        .admin-overview-activity:last-child {
            border-bottom: 0;
        }

        .admin-overview-activity:hover {
            padding-left: 8px;
            background: #fff8f9;
            transform: translateX(3px);
        }

        .admin-overview-activity__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            color: #9f1239;
            background: #fbecef;
            border-radius: 11px;
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-overview-activity:hover
        .admin-overview-activity__icon {
            transform: rotate(-5deg) scale(1.08);
            box-shadow: 0 7px 15px rgba(159, 18, 57, 0.10);
        }

        .admin-overview-activity__icon svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-overview-activity__content {
            min-width: 0;
            flex: 1;
        }

        .admin-overview-activity__content strong {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
            line-height: 1.35;
            color: #29211e;
        }

        .admin-overview-activity__content p {
            margin: 0 0 5px;
            font-size: 12px;
            line-height: 1.45;
            color: #756863;
        }

        .admin-overview-activity__content span {
            font-size: 11px;
            color: #a0938d;
        }

        /*
        |--------------------------------------------------------------------------
        | TANGGAL AKTIVITAS
        |--------------------------------------------------------------------------
        */

        .admin-overview-date {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 69px;
            padding: 10px 3px;
            border-bottom: 1px solid #f0e9e6;
            cursor: pointer;
            transition:
                background-color 0.2s ease,
                transform 0.2s ease,
                padding-left 0.2s ease;
        }

        .admin-overview-date:last-child {
            border-bottom: 0;
        }

        .admin-overview-date:hover {
            padding-left: 8px;
            background: #fff8f9;
            transform: translateX(3px);
        }

        .admin-overview-date__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            color: #9f1239;
            background: #fbecef;
            border-radius: 10px;
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease;
        }

        .admin-overview-date:hover
        .admin-overview-date__icon {
            transform: scale(1.08);
            box-shadow: 0 6px 14px rgba(159, 18, 57, 0.10);
        }

        .admin-overview-date__icon svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .admin-overview-date__content {
            min-width: 0;
            flex: 1;
        }

        .admin-overview-date__content strong {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
            line-height: 1.35;
            color: #29211e;
        }

        .admin-overview-date__content span {
            font-size: 11px;
            color: #958881;
        }

        .admin-overview-date__count {
            flex: 0 0 auto;
            min-width: 32px;
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: #9f1239;
        }

        .admin-overview-empty {
            padding: 28px 8px;
            text-align: center;
            font-size: 12px;
            color: #958881;
        }

        .admin-overview-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 22px;
            padding: 14px 15px;
            border: 1px solid #e6e2df;
            border-radius: 12px;
            background: #faf9f8;
            font-size: 11px;
            line-height: 1.5;
            color: #958881;
            cursor: pointer;
            transition:
                background-color 0.22s ease,
                border-color 0.22s ease,
                color 0.22s ease;
        }

        .admin-overview-note:hover {
            background: #fff6f8;
            border-color: #e8c9d1;
            color: #8f5260;
        }

        .admin-overview-note > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex: 0 0 20px;
            border: 1px solid #d9ccc6;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 800;
            color: #9f1239;
            background: #fff8f9;
            transition:
                transform 0.22s ease,
                background-color 0.22s ease,
                border-color 0.22s ease;
        }

        .admin-overview-note:hover > span {
            transform: rotate(8deg) scale(1.08);
            background: #fbecef;
            border-color: #e7b9c5;
        }

        .admin-overview-note p {
            margin: 0;
        }


        /* =========================================================
           RESPONSIVE
           ========================================================= */

        @media (max-width: 900px) {

            .admin-course-columns {
                display: none;
            }

            .admin-course-item {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 17px 4px;
            }

            .admin-overview-grid {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 760px) {

            .admin-submission-panel,
            .admin-activity-panel,
            .admin-quick-panel,
            .admin-course-panel,
            .admin-overview-panel {
                padding: 25px 22px 23px;
            }

            .admin-submission-header,
            .admin-activity-header,
            .admin-course-header,
            .admin-overview-header {
                flex-direction: column;
            }

            .admin-submission-total,
            .admin-course-total {
                text-align: left;
            }

            .admin-submission-body {
                grid-template-columns: 1fr;
                gap: 22px;
            }

            .admin-submission-progress {
                padding-right: 0;
            }

            .admin-submission-status {
                border-left: 0;
                border-top: 1px solid #eee4df;
            }

            .admin-submission-status__item {
                padding: 0;
            }

            .admin-submission-status__item:hover {
                padding-left: 5px;
            }

            .admin-activity-item {
                align-items: flex-start;
            }

            .admin-activity-time {
                white-space: normal;
                text-align: right;
            }

            .admin-quick-actions {
                grid-template-columns: 1fr;
            }

            .admin-overview-summary {
                grid-template-columns: 1fr;
            }

            .admin-overview-column {
                padding: 17px;
            }

        }

    </style>


    <section class="admin-dashboard">

        {{-- =====================================================
             HEADER
             ===================================================== --}}

        <header class="admin-dashboard__header">

            <div>

                <p class="admin-dashboard__eyebrow">
                    KAMPUSLMS / ADMIN
                </p>

                <h1 class="admin-dashboard__title">
                    Dashboard
                </h1>

                <p class="admin-dashboard__subtitle">
                    Pantau data dan aktivitas pembelajaran KampusLMS.
                </p>

            </div>

            <div class="admin-dashboard__date">

                <span class="admin-dashboard__date-dot"></span>

                Sistem aktif

            </div>

        </header>


        {{-- =====================================================
             STATISTIK UTAMA
             ===================================================== --}}

        <section class="admin-metrics">

            <article class="admin-metric admin-metric--pink">

                <div class="admin-metric__top">

                    <div class="admin-metric__icon">

                        <svg viewBox="0 0 24 24">

                            <circle cx="9" cy="7" r="4"></circle>

                            <path d="M3 21v-2a6 6 0 0 1 12 0v2"></path>

                            <path d="M16 3.5a4 4 0 0 1 0 7"></path>

                            <path d="M18 14a5 5 0 0 1 3 4.6V21"></path>

                        </svg>

                    </div>

                    <span class="admin-metric__index">
                        01
                    </span>

                </div>

                <div class="admin-metric__number">
                    {{ $totalDosen }}
                </div>

                <div class="admin-metric__label">
                    Dosen
                </div>

                <div class="admin-metric__caption">
                    Pengajar yang mengelola kegiatan pembelajaran.
                </div>

            </article>


            <article class="admin-metric admin-metric--purple">

                <div class="admin-metric__top">

                    <div class="admin-metric__icon">

                        <svg viewBox="0 0 24 24">

                            <path d="M2 10l10-5 10 5-10 5L2 10z"></path>

                            <path d="M6 12.5V17c2.5 2 9.5 2 12 0v-4.5"></path>

                            <path d="M22 10v6"></path>

                        </svg>

                    </div>

                    <span class="admin-metric__index">
                        02
                    </span>

                </div>

                <div class="admin-metric__number">
                    {{ $totalMahasiswa }}
                </div>

                <div class="admin-metric__label">
                    Mahasiswa
                </div>

                <div class="admin-metric__caption">
                    Peserta yang mengikuti kegiatan belajar di KampusLMS.
                </div>

            </article>


            <article class="admin-metric admin-metric--blue">

                <div class="admin-metric__top">

                    <div class="admin-metric__icon">

                        <svg viewBox="0 0 24 24">

                            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path>

                            <path d="M4 5.5v16"></path>

                            <path d="M8 7h8"></path>

                            <path d="M8 11h6"></path>

                        </svg>

                    </div>

                    <span class="admin-metric__index">
                        03
                    </span>

                </div>

                <div class="admin-metric__number">
                    {{ $totalMataKuliah }}
                </div>

                <div class="admin-metric__label">
                    Mata Kuliah
                </div>

                <div class="admin-metric__caption">
                    Ruang belajar untuk kegiatan perkuliahan.
                </div>

            </article>


            <article class="admin-metric admin-metric--orange">

                <div class="admin-metric__top">

                    <div class="admin-metric__icon">

                        <svg viewBox="0 0 24 24">

                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>

                            <path d="M14 2v6h6"></path>

                            <path d="M8 13h8"></path>

                            <path d="M8 17h5"></path>

                        </svg>

                    </div>

                    <span class="admin-metric__index">
                        04
                    </span>

                </div>

                <div class="admin-metric__number">
                    {{ $totalTugas }}
                </div>

                <div class="admin-metric__label">
                    Tugas
                </div>

                <div class="admin-metric__caption">
                    Aktivitas yang dikerjakan dan dikumpulkan mahasiswa.
                </div>

            </article>

        </section>


        {{-- =====================================================
             TUGAS
             ===================================================== --}}

        <section class="admin-task-panel">

            <div class="admin-task-panel__heading">

                <div>

                    <p class="admin-section-eyebrow">
                        PEMBELAJARAN
                    </p>

                    <h2 class="admin-section-title">
                        Tugas
                    </h2>

                    <p class="admin-section-description">
                        Melihat tugas yang sudah siap dikerjakan dan yang masih disimpan untuk disiapkan.
                    </p>

                </div>

                <div class="admin-task-total">

                    <span>
                        Jumlah tugas
                    </span>

                    <strong>
                        {{ $totalTugas }}
                    </strong>

                </div>

            </div>


            <div class="admin-task-progress">

                <div class="admin-task-progress__top">

                    <span>
                        Tugas yang sudah diterbitkan
                    </span>

                    <strong>
                        {{ $persentasePublished }}%
                    </strong>

                </div>

                <div class="admin-task-progress__track">

                    <div
                        class="admin-task-progress__bar"
                        style="width: {{ $persentasePublished }}%;"
                    ></div>

                </div>

            </div>


            <div class="admin-task-row">

                <div class="admin-task-row__identity">

                    <div class="admin-task-row__icon admin-task-row__icon--published">

                        <svg viewBox="0 0 24 24">

                            <path d="M20 6L9 17l-5-5"></path>

                        </svg>

                    </div>

                    <div>

                        <h3>
                            Tugas diterbitkan
                        </h3>

                        <p>
                            Sudah dapat dilihat dan dikerjakan mahasiswa.
                        </p>

                    </div>

                </div>

                <div class="admin-task-row__count">
                    {{ $tugasPublished }}
                </div>

            </div>


            <div class="admin-task-row admin-task-row--draft">

                <div class="admin-task-row__identity">

                    <div class="admin-task-row__icon admin-task-row__icon--draft">

                        <svg viewBox="0 0 24 24">

                            <path d="M12 20h9"></path>

                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z"></path>

                        </svg>

                    </div>

                    <div>

                        <h3>
                            Tersimpan sebagai draf
                        </h3>

                        <p>
                            Masih disiapkan dan belum diberikan kepada mahasiswa.
                        </p>

                    </div>

                </div>

                <div class="admin-task-row__count">
                    {{ $tugasDraft }}
                </div>

            </div>

        </section>


        {{-- =====================================================
             PEMANTAUAN PENGUMPULAN
             ===================================================== --}}

        <section class="admin-submission-panel">

            <div class="admin-submission-header">

                <div>

                    <p class="admin-submission-eyebrow">
                        PENGUMPULAN TUGAS
                    </p>

                    <h2 class="admin-submission-title">
                        Pemantauan Pengumpulan
                    </h2>

                    <p class="admin-submission-description">
                        Melihat seberapa banyak tugas yang sudah dikumpulkan mahasiswa dan berapa yang masih menunggu penilaian.
                    </p>

                </div>

                <div class="admin-submission-total">

                    <span>
                        Jumlah pengumpulan
                    </span>

                    <strong>
                        {{ $totalPengumpulan }}
                    </strong>

                </div>

            </div>


            <div class="admin-submission-body">

                <div class="admin-submission-progress">

                    <div class="admin-submission-progress__top">

                        <span class="admin-submission-progress__label">
                            Pengumpulan yang sudah dinilai
                        </span>

                        <strong class="admin-submission-progress__percent">
                            {{ $persentaseDinilaiTampilan }}%
                        </strong>

                    </div>

                    <div class="admin-submission-progress__track">

                        <div
                            class="admin-submission-progress__bar"
                            style="width: {{ $persentaseDinilai }}%;"
                        ></div>

                    </div>

                    <p class="admin-submission-progress__caption">
                        Sebanyak {{ $pengumpulanDinilai }} pengumpulan sudah selesai dinilai dari {{ $totalPengumpulan }} pengumpulan.
                    </p>

                </div>


                <div class="admin-submission-status">

                    <div class="admin-submission-status__item">

                        <div class="admin-submission-status__identity">

                            <div class="admin-submission-status__icon admin-submission-status__icon--done">

                                <svg viewBox="0 0 24 24">

                                    <path d="M20 6L9 17l-5-5"></path>

                                </svg>

                            </div>

                            <span class="admin-submission-status__label">
                                Sudah dinilai
                            </span>

                        </div>

                        <strong class="admin-submission-status__count">
                            {{ $pengumpulanDinilai }}
                        </strong>

                    </div>


                    <div class="admin-submission-status__item">

                        <div class="admin-submission-status__identity">

                            <div class="admin-submission-status__icon admin-submission-status__icon--pending">

                                <svg viewBox="0 0 24 24">

                                    <circle cx="12" cy="12" r="9"></circle>

                                    <path d="M12 7v5l3 2"></path>

                                </svg>

                            </div>

                            <span class="admin-submission-status__label">
                                Menunggu penilaian
                            </span>

                        </div>

                        <strong class="admin-submission-status__count">
                            {{ $pengumpulanMenunggu }}
                        </strong>

                    </div>

                </div>

            </div>


            <div class="admin-submission-foot">

                <span class="admin-submission-foot__mark">
                    i
                </span>

                <span>
                    Data dihitung dari pengumpulan yang sudah memiliki nilai dan yang masih menunggu penilaian.
                </span>

            </div>

        </section>


        {{-- =====================================================
             AKTIVITAS TERBARU
             ===================================================== --}}

        <section class="admin-activity-panel">

            <div class="admin-activity-header">

                <div>

                    <p class="admin-activity-eyebrow">
                        AKTIVITAS
                    </p>

                    <h2 class="admin-activity-title">
                        Aktivitas Terbaru
                    </h2>

                    <p class="admin-activity-description">
                        Melihat aktivitas terbaru yang terjadi dalam kegiatan pembelajaran KampusLMS.
                    </p>

                </div>

            </div>


            <div class="admin-activity-list">

                @forelse ($aktivitasTerbaru as $aktivitas)

                    <div class="admin-activity-item">

                        <div class="admin-activity-icon">

                            <svg viewBox="0 0 24 24">

                                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>

                                <path d="M8 10h8"></path>

                                <path d="M8 14h5"></path>

                            </svg>

                        </div>

                        <div class="admin-activity-content">

                            <strong>
                                {{ $aktivitas->student?->name ?? 'Mahasiswa' }}
                            </strong>

                            <p>
                                mengumpulkan tugas
                                "{{ $aktivitas->assignment?->title ?? 'Tugas' }}"
                            </p>

                        </div>

                        <time class="admin-activity-time">
                            {{ $aktivitas->submitted_at?->diffForHumans() ?? 'Baru saja' }}
                        </time>

                    </div>

                @empty

                    <div class="admin-activity-empty">
                        Belum ada aktivitas pengumpulan tugas.
                    </div>

                @endforelse

            </div>

        </section>


        {{-- =====================================================
             QUICK ACTION
             ===================================================== --}}

        <section class="admin-quick-panel">

            <div class="admin-quick-header">

                <p class="admin-quick-eyebrow">
                    AKSES CEPAT
                </p>

                <h2 class="admin-quick-title">
                    Quick Action
                </h2>

                <p class="admin-quick-description">
                    Akses langsung ke bagian administrasi yang paling sering digunakan.
                </p>

            </div>


            <div class="admin-quick-actions">

                <a
                    href="{{ route('admin.users.index') }}"
                    class="admin-quick-action"
                >

                    <div class="admin-quick-action__icon">

                        <svg viewBox="0 0 24 24">

                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>

                            <circle cx="9" cy="7" r="4"></circle>

                            <path d="M19 8v6"></path>

                            <path d="M22 11h-6"></path>

                        </svg>

                    </div>

                    <div class="admin-quick-action__content">

                        <strong>
                            Kelola Pengguna
                        </strong>

                        <span>
                            Melihat dan mengelola data pengguna KampusLMS.
                        </span>

                    </div>

                    <span class="admin-quick-action__arrow">
                        →
                    </span>

                </a>


                <a
                    href="{{ route('admin.mata-kuliah.index') }}"
                    class="admin-quick-action"
                >

                    <div class="admin-quick-action__icon">

                        <svg viewBox="0 0 24 24">

                            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path>

                            <path d="M4 5.5v16"></path>

                            <path d="M8 7h8"></path>

                            <path d="M8 11h6"></path>

                        </svg>

                    </div>

                    <div class="admin-quick-action__content">

                        <strong>
                            Kelola Mata Kuliah
                        </strong>

                        <span>
                            Melihat dan mengelola daftar mata kuliah KampusLMS.
                        </span>

                    </div>

                    <span class="admin-quick-action__arrow">
                        →
                    </span>

                </a>

            </div>

        </section>


        {{-- =====================================================
             RINGKASAN MATA KULIAH
             ===================================================== --}}

        <section class="admin-course-panel">

            <div class="admin-course-header">

                <div>

                    <p class="admin-course-eyebrow">
                        PEMBELAJARAN
                    </p>

                    <h2 class="admin-course-title">
                        Ringkasan Mata Kuliah
                    </h2>

                    <p class="admin-course-description">
                        Daftar mata kuliah yang tersedia di KampusLMS beserta dosen pengampu, jumlah SKS, dan jumlah mahasiswa yang terdaftar.
                    </p>

                </div>

                <div class="admin-course-total">

                    <span>
                        Total mata kuliah
                    </span>

                    <strong>
                        {{ $totalMataKuliah }}
                    </strong>

                </div>

            </div>


            <div class="admin-course-columns">

                <span>
                    Mata Kuliah
                </span>

                <span>
                    Dosen Pengampu
                </span>

                <span>
                    Beban SKS
                </span>

                <span>
                    Mahasiswa Terdaftar
                </span>

            </div>


            <div class="admin-course-list">

                @forelse ($ringkasanMataKuliah as $course)

                    <div class="admin-course-item">

                        <div class="admin-course-name">

                            <span class="admin-course-code">
                                {{ $course->code }}
                            </span>

                            <strong>
                                {{ $course->name }}
                            </strong>

                        </div>


                        <div class="admin-course-lecturer">

                            {{ $course->lecturer?->name ?? 'Belum ditentukan' }}

                        </div>


                        <div class="admin-course-sks">

                            {{ $course->sks }} SKS

                        </div>


                        <div class="admin-course-students">

                            {{ $course->students_count }} mahasiswa

                        </div>

                    </div>

                @empty

                    <div class="admin-course-empty">
                        Belum ada mata kuliah yang tersedia.
                    </div>

                @endforelse

            </div>

        </section>


        {{-- =====================================================
             FITUR 7 — OVERVIEW AKTIVITAS
             ===================================================== --}}

        <section class="admin-overview-panel">

            <div class="admin-overview-header">

                <div>

                    <p class="admin-overview-eyebrow">
                        OVERVIEW AKTIVITAS
                    </p>

                    <h2 class="admin-overview-title">
                        Aktivitas Pembelajaran
                    </h2>

                    <p class="admin-overview-description">
                        Ringkasan aktivitas pengumpulan tugas yang tercatat dalam KampusLMS.
                    </p>

                </div>

            </div>


            <div class="admin-overview-summary">

                <div class="admin-overview-card">

                    <span class="admin-overview-card__label">
                        Hari ini
                    </span>

                    <strong class="admin-overview-card__number">
                        {{ $aktivitasHariIni }}
                    </strong>

                    <span class="admin-overview-card__caption">
                        pengumpulan tugas
                    </span>

                </div>


                <div class="admin-overview-card">

                    <span class="admin-overview-card__label">
                        Minggu ini
                    </span>

                    <strong class="admin-overview-card__number">
                        {{ $aktivitasMingguIni }}
                    </strong>

                    <span class="admin-overview-card__caption">
                        pengumpulan tugas
                    </span>

                </div>


                <div class="admin-overview-card">

                    <span class="admin-overview-card__label">
                        Bulan ini
                    </span>

                    <strong class="admin-overview-card__number">
                        {{ $aktivitasBulanIni }}
                    </strong>

                    <span class="admin-overview-card__caption">
                        pengumpulan tugas
                    </span>

                </div>

            </div>


            <div class="admin-overview-grid">

                <div class="admin-overview-column">

                    <div class="admin-overview-section-title">

                        <div>

                            <h3>
                                Aktivitas Terbaru
                            </h3>

                            <p>
                                Lima pengumpulan tugas terakhir yang tercatat.
                            </p>

                        </div>

                        <span>
                            5 Terakhir
                        </span>

                    </div>


                    @forelse ($aktivitasTerbaru as $aktivitas)

                        <div class="admin-overview-activity">

                            <div class="admin-overview-activity__icon">

                                <svg viewBox="0 0 24 24">

                                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>

                                    <path d="M8 10h8"></path>

                                    <path d="M8 14h5"></path>

                                </svg>

                            </div>


                            <div class="admin-overview-activity__content">

                                <strong>
                                    {{ $aktivitas->student?->name ?? 'Mahasiswa' }}
                                </strong>

                                <p>
                                    Mengumpulkan tugas
                                    "{{ $aktivitas->assignment?->title ?? 'Tugas' }}"
                                </p>

                                <span>
                                    {{ $aktivitas->submitted_at?->format('d M Y, H:i') ?? '-' }}
                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="admin-overview-empty">
                            Belum ada aktivitas pengumpulan tugas.
                        </div>

                    @endforelse

                </div>


                <div class="admin-overview-column">

                    <div class="admin-overview-section-title">

                        <div>

                            <h3>
                                Tanggal Aktivitas
                            </h3>

                            <p>
                                Tanggal dengan pengumpulan terbaru.
                            </p>

                        </div>

                        <span>
                            7 Terbaru
                        </span>

                    </div>


                    @forelse ($tanggalAktivitas as $tanggal)

                        <div class="admin-overview-date">

                            <div class="admin-overview-date__icon">

                                <svg viewBox="0 0 24 24">

                                    <rect
                                        x="3"
                                        y="4"
                                        width="18"
                                        height="17"
                                        rx="2"
                                    ></rect>

                                    <path d="M16 2v4"></path>

                                    <path d="M8 2v4"></path>

                                    <path d="M3 10h18"></path>

                                </svg>

                            </div>


                            <div class="admin-overview-date__content">

                                <strong>
                                    {{ \Carbon\Carbon::parse($tanggal->tanggal)->translatedFormat('d F Y') }}
                                </strong>

                                <span>
                                    Total pengumpulan pada tanggal tersebut
                                </span>

                            </div>

                            <strong class="admin-overview-date__count">
                                {{ $tanggal->jumlah }}
                            </strong>

                        </div>

                    @empty

                        <div class="admin-overview-empty">
                            Belum ada tanggal aktivitas.
                        </div>

                    @endforelse

                </div>

            </div>


            <div class="admin-overview-note">

                <span>
                    i
                </span>

                <p>
                    Data aktivitas dihitung langsung dari waktu pengumpulan tugas yang tersimpan di KampusLMS.
                </p>

            </div>

        </section>


        {{-- =====================================================
             CATATAN
             ===================================================== --}}

        <div class="admin-dashboard-note">

            <span class="admin-dashboard-note__mark">
                i
            </span>

            <p>
                Data pada dashboard diperbarui langsung dari data KampusLMS.
            </p>

        </div>

    </section>

</x-layout>