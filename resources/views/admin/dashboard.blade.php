@php
    $persentaseDinilaiTampilan = number_format($persentaseDinilai, 2, ',', '.');
@endphp

<x-layout title="Dashboard Admin" role="admin" full-footer>
<style>
/* Statistik utama — khusus Dashboard Admin */
.admin-dashboard .admin-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    width: 100%;
}

.admin-dashboard .admin-metric {
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
    min-height: 218px;
    padding: 1.35rem;
    background: #fff;
    border: 1px solid #eadfe5;
    border-radius: 24px;
    box-shadow: 0 7px 22px rgba(42, 25, 38, .035);
    transition: transform .2s, box-shadow .2s;
}

.admin-dashboard .admin-metric::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: currentColor;
    opacity: .85;
}

.admin-dashboard .admin-metric::after {
    content: "";
    position: absolute;
    right: -32px;
    bottom: -42px;
    width: 125px;
    height: 125px;
    border-radius: 50%;
    background: currentColor;
    opacity: .055;
    pointer-events: none;
    transition: transform .4s ease, opacity .3s ease;
}

.admin-dashboard .admin-metric:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 32px rgba(42, 25, 38, .08);
}

.admin-dashboard .admin-metric:hover::after {
    transform: translate(-12px, -10px) scale(1.2);
    opacity: .12;
}

.admin-dashboard .admin-metric--pink { color: #d41463; }
.admin-dashboard .admin-metric--purple { color: #7c3aed; }
.admin-dashboard .admin-metric--blue { color: #2563eb; }
.admin-dashboard .admin-metric--orange { color: #ea580c; }

.admin-dashboard .admin-metric__top {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.admin-dashboard .admin-metric__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: currentColor;
    color: #fff;
}

.admin-dashboard .admin-metric__icon svg {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.admin-dashboard .admin-metric__index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 27px;
    padding: 0 .65rem;
    border: 1px solid currentColor;
    border-radius: 999px;
    background: #fff;
    font-size: .7rem;
    font-weight: 850;
    letter-spacing: .13em;
    color: currentColor;
}

.admin-dashboard .admin-metric__number {
    position: relative;
    z-index: 1;
    margin: 0;
    font-size: 3rem;
    line-height: .95;
    font-weight: 850;
    letter-spacing: -.055em;
    color: #241b2f;
}

.admin-dashboard .admin-metric__label {
    position: relative;
    z-index: 1;
    margin-top: .48rem;
    font-size: .98rem;
    line-height: 1.25;
    font-weight: 850;
    color: #241b2f;
}

.admin-dashboard .admin-metric__caption {
    position: relative;
    z-index: 1;
    max-width: 92%;
    margin-top: .38rem;
    font-size: .74rem;
    line-height: 1.45;
    color: #756b78;
}

/* Panel utama */
.admin-dashboard .admin-task-panel,
.admin-dashboard .admin-submission-panel,
.admin-dashboard .admin-activity-panel,
.admin-dashboard .admin-quick-panel,
.admin-dashboard .admin-course-panel {
    position: relative;
    margin-top: 22px;
    padding: 30px 32px 28px;
    background: #fff;
    border: 1px solid #eadfd9;
    border-radius: 24px;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
}

.admin-dashboard .admin-task-panel:hover,
.admin-dashboard .admin-submission-panel:hover,
.admin-dashboard .admin-activity-panel:hover,
.admin-dashboard .admin-quick-panel:hover,
.admin-dashboard .admin-course-panel:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 40px rgba(86, 45, 35, .08);
    border-color: #e9c4d0;
}

.admin-dashboard .admin-submission-panel::before,
.admin-dashboard .admin-activity-panel::before,
.admin-dashboard .admin-quick-panel::before,
.admin-dashboard .admin-course-panel::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
        width: 6px;
    height: 100%;
}

.admin-dashboard .admin-submission-panel::before {
    background: #9f1239;
}

.admin-dashboard .admin-activity-panel::before {
    background: #c24168;
}

.admin-dashboard .admin-quick-panel::before,
.admin-dashboard .admin-course-panel::before {
    background: linear-gradient(180deg, #c24168, #e879a0);
}

.admin-dashboard .admin-task-panel__heading,
.admin-dashboard .admin-submission-header,
.admin-dashboard .admin-activity-header,
.admin-dashboard .admin-course-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 30px;
    padding-bottom: 22px;
    border-bottom: 1px solid #eee4df;
}

.admin-dashboard .admin-section-eyebrow,
.admin-dashboard .admin-submission-eyebrow,
.admin-dashboard .admin-activity-eyebrow,
.admin-dashboard .admin-quick-eyebrow,
.admin-dashboard .admin-course-eyebrow {
    margin: 0 0 7px;
    color: #9f1239;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .16em;
}

.admin-dashboard .admin-section-title,
.admin-dashboard .admin-submission-title,
.admin-dashboard .admin-activity-title,
.admin-dashboard .admin-quick-title,
.admin-dashboard .admin-course-title {
    margin: 0;
    color: #172033;
    font-size: 25px;
    line-height: 1.15;
}

.admin-dashboard .admin-section-description,
.admin-dashboard .admin-submission-description,
.admin-dashboard .admin-activity-description,
.admin-dashboard .admin-quick-description,
.admin-dashboard .admin-course-description {
    max-width: 600px;
    margin: 8px 0 0;
    color: #7c6f69;
    font-size: 13px;
    line-height: 1.6;
}

/* Kotak jumlah dibuat konsisten */
.admin-dashboard .admin-task-total,
.admin-dashboard .admin-submission-total,
.admin-dashboard .admin-course-total {
    box-sizing: border-box;
    display: flex;
    flex: 0 0 180px;
    flex-direction: column;
    justify-content: center;
    width: 180px;
    min-width: 180px;
    min-height: 90px;
    padding: 14px 17px;
    text-align: right;
    background: linear-gradient(135deg, #fff1f4 0%, #fce7f3 58%, #f3e8ff 100%);
    border: 1px solid #efcbd9;
    border-radius: 15px;
    box-shadow: 0 3px 10px rgba(159, 18, 57, .035);
    transition: transform .22s, box-shadow .22s, background .22s;
}

.admin-dashboard .admin-task-total:hover,
.admin-dashboard .admin-submission-total:hover,
.admin-dashboard .admin-course-total:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 24px rgba(159, 18, 57, .1);
    background: linear-gradient(135deg, #ffe4ec, #fce7f3 58%, #ede9fe);
}

.admin-dashboard .admin-task-total span,
.admin-dashboard .admin-submission-total span,
.admin-dashboard .admin-course-total span {
    display: block;
    margin-bottom: 7px;
    color: #8f5265;
    font-size: 11px;
    line-height: 1.35;
    font-weight: 700;
}

.admin-dashboard .admin-task-total strong,
.admin-dashboard .admin-submission-total strong,
.admin-dashboard .admin-course-total strong {
    display: block;
    color: #9f1239;
    font-size: 32px;
    line-height: 1;
    font-weight: 850;
    letter-spacing: -.035em;
    font-variant-numeric: tabular-nums;
}

/* Statistik penerbitan tugas */
.admin-dashboard .admin-task-progress {
    padding: 23px 0 18px;
    border-radius: 12px;
    transition: background .25s ease, padding .25s ease;
}

.admin-dashboard .admin-task-progress:hover {
    padding-right: 10px;
    padding-left: 10px;
    background: #fffafb;
}

.admin-dashboard .admin-task-progress__top {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 10px;
    color: #3f3531;
    font-size: 13px;
}

.admin-dashboard .admin-task-progress__top strong {
    color: #9f1239;
    font-size: 18px;
    transition: transform .2s ease;
}

.admin-dashboard .admin-task-progress:hover .admin-task-progress__top strong {
    transform: scale(1.08);
}

.admin-dashboard .admin-task-progress__track,
.admin-dashboard .admin-submission-progress__track {
    width: 100%;
    height: 11px;
    overflow: hidden;
    background: #f0e9e6;
    border-radius: 99px;
}

.admin-dashboard .admin-task-progress__bar,
.admin-dashboard .admin-submission-progress__bar {
    height: 100%;
    background: linear-gradient(90deg, #9f1239, #c24168, #e879a0);
    border-radius: 99px;
    transition: width .6s ease, filter .2s ease;
}

.admin-dashboard .admin-task-progress:hover .admin-task-progress__bar,
.admin-dashboard .admin-submission-progress:hover .admin-submission-progress__bar {
    filter: saturate(1.25);
}

.admin-dashboard .admin-task-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 17px 0;
    border-top: 1px solid #eee4df;
    transition: padding .2s ease, background .2s ease;
}

.admin-dashboard .admin-task-row:hover {
    padding-right: 10px;
    padding-left: 10px;
    background: #fffafb;
}

.admin-dashboard .admin-task-row__identity {
    display: flex;
    align-items: center;
    gap: 13px;
}

.admin-dashboard .admin-task-row__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    border-radius: 12px;
    transition: transform .25s ease;
}

.admin-dashboard .admin-task-row:hover .admin-task-row__icon {
    transform: rotate(-5deg) scale(1.06);
}

.admin-dashboard .admin-task-row__icon svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.admin-dashboard .admin-task-row__icon--published {
    color: #16804b;
    background: #e9f7ef;
}

.admin-dashboard .admin-task-row__icon--draft {
    color: #a16207;
    background: #fff4d8;
}

.admin-dashboard .admin-task-row h3 {
    margin: 0 0 4px;
    color: #29211e;
    font-size: 13px;
}

.admin-dashboard .admin-task-row p {
    margin: 0;
    color: #756863;
    font-size: 12px;
    line-height: 1.45;
}

.admin-dashboard .admin-task-row__count {
    color: #172033;
    font-size: 24px;
    font-weight: 800;
}

/* Pemantauan pengumpulan */
.admin-dashboard .admin-submission-body {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 220px;
    gap: 34px;
    padding-top: 27px;
}

.admin-dashboard .admin-submission-progress {
    padding-right: 30px;
    border-radius: 12px;
    transition: background .25s ease, padding .25s ease;
}

.admin-dashboard .admin-submission-progress:hover {
    padding-left: 10px;
    background: #fffafb;
}

.admin-dashboard .admin-submission-progress__top {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 10px;
}

.admin-dashboard .admin-submission-progress__label {
    color: #3f3531;
    font-size: 13px;
    font-weight: 600;
}

.admin-dashboard .admin-submission-progress__percent {
    color: #9f1239;
    font-size: 24px;
    font-weight: 800;
    transition: transform .2s ease;
}

.admin-dashboard .admin-submission-progress:hover .admin-submission-progress__percent {
    transform: scale(1.08);
}

.admin-dashboard .admin-submission-progress__caption {
    margin: 11px 0 0;
    color: #958881;
    font-size: 12px;
    line-height: 1.6;
}

.admin-dashboard .admin-submission-status {
    display: flex;
    flex-direction: column;
    border-left: 1px solid #eee4df;
}

.admin-dashboard .admin-submission-status__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    min-height: 82px;
    padding-left: 24px;
    border-radius: 13px;
    transition: background .2s, transform .2s;
}

.admin-dashboard .admin-submission-status__item + .admin-submission-status__item {
    border-top: 1px solid #eee4df;
}

.admin-dashboard .admin-submission-status__item:hover {
    padding-left: 29px;
    background: #fffafa;
    transform: translateX(4px);
}

.admin-dashboard .admin-submission-status__identity {
    display: flex;
    align-items: center;
    gap: 11px;
}

.admin-dashboard .admin-submission-status__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    flex: 0 0 36px;
    border-radius: 11px;
}

.admin-dashboard .admin-submission-status__icon svg,
.admin-dashboard .admin-activity-icon svg,
.admin-dashboard .admin-quick-action__icon svg {
    width: 18px;
    height: 18px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.admin-dashboard .admin-submission-status__icon--done {
    color: #16804b;
    background: #e9f7ef;
}

.admin-dashboard .admin-submission-status__icon--pending {
    color: #a16207;
    background: #fff4d8;
}

.admin-dashboard .admin-submission-status__label {
    color: #675a55;
    font-size: 12px;
    font-weight: 600;
}

.admin-dashboard .admin-submission-status__count {
    min-width: 38px;
    color: #172033;
    text-align: right;
    font-size: 24px;
    line-height: 1;
    font-weight: 800;
}

.admin-dashboard .admin-submission-foot {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
    padding-top: 17px;
    border-top: 1px solid #eee4df;
    color: #958881;
    font-size: 11px;
    line-height: 1.5;
}

.admin-dashboard .admin-submission-foot__mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    flex: 0 0 20px;
    color: #9f1239;
    background: #fff8f9;
    border: 1px solid #d9ccc6;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 700;
}

/* Aktivitas terbaru */
.admin-dashboard .admin-activity-list {
    display: flex;
    flex-direction: column;
}

.admin-dashboard .admin-activity-item {
    display: flex;
    align-items: center;
    gap: 16px;
    min-height: 76px;
    padding: 14px 4px;
    border-bottom: 1px solid #eee4df;
    transition: background .2s, transform .2s;
}

.admin-dashboard .admin-activity-item:last-child {
    border-bottom: 0;
}

.admin-dashboard .admin-activity-item:hover {
    background: #fffafa;
    transform: translateX(4px);
}

.admin-dashboard .admin-activity-icon {
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

.admin-dashboard .admin-activity-content {
    min-width: 0;
    flex: 1;
}

.admin-dashboard .admin-activity-content strong {
    display: block;
    margin-bottom: 3px;
    color: #29211e;
    font-size: 13px;
}

.admin-dashboard .admin-activity-content p {
    margin: 0;
    color: #756863;
    font-size: 12px;
    line-height: 1.45;
}

.admin-dashboard .admin-activity-time {
    flex: 0 0 auto;
    color: #9a8d87;
    font-size: 11px;
    white-space: nowrap;
}

.admin-dashboard .admin-activity-empty,
.admin-dashboard .admin-course-empty {
    padding: 32px 10px;
    color: #958881;
    text-align: center;
    font-size: 13px;
}

/* Akses cepat — warna konsisten dengan tema marun/pink */
.admin-dashboard .admin-quick-panel {
    background: linear-gradient(135deg, #fff 0%, #fff8fa 58%, #fff4f6 100%);
    border-color: #ead9e0;
}

.admin-dashboard .admin-quick-header {
    padding-bottom: 22px;
    border-bottom: 1px solid #f0dfe5;
}

.admin-dashboard .admin-quick-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    padding-top: 22px;
}

.admin-dashboard .admin-quick-action {
    display: flex;
    align-items: center;
    gap: 15px;
    min-height: 78px;
    padding: 16px 18px;
    color: inherit;
    text-decoration: none;
    background: linear-gradient(135deg, #fff, #fff7f9);
    border: 1px solid #ead9e0;
    border-radius: 16px;
    transition: transform .2s, border-color .2s, background .2s, box-shadow .2s;
}

.admin-dashboard .admin-quick-action:hover {
    transform: translateY(-3px);
    background: #fbecef;
    border-color: #e7b9c5;
    box-shadow: 0 8px 20px rgba(159, 18, 57, .1);
}

.admin-dashboard .admin-quick-action__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    color: #9f1239;
    background: linear-gradient(135deg, #fbecef, #fce7f3);
    border-radius: 12px;
}

.admin-dashboard .admin-quick-action__content {
    min-width: 0;
    flex: 1;
}

.admin-dashboard .admin-quick-action__content strong {
    display: block;
    margin-bottom: 3px;
    color: #29211e;
    font-size: 13px;
}

.admin-dashboard .admin-quick-action__content span {
    display: block;
        color: #81736d;
    font-size: 11px;
    line-height: 1.45;
}

.admin-dashboard .admin-quick-action__arrow {
    color: #9f1239;
    font-size: 18px;
    transition: transform .2s;
}

.admin-dashboard .admin-quick-action:hover .admin-quick-action__arrow {
    transform: translateX(4px);
}

/* Ringkasan mata kuliah — palet warna seragam */
.admin-dashboard .admin-course-panel {
    background: linear-gradient(135deg, #fff 0%, #fff8fa 58%, #fff4f6 100%);
    border-color: #ead9e0;
}

.admin-dashboard .admin-course-columns,
.admin-dashboard .admin-course-item {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(180px, 1fr) 90px 150px;
    gap: 20px;
    align-items: center;
}

.admin-dashboard .admin-course-columns {
    margin-top: 20px;
    padding: 0 4px 11px;
    border-bottom: 1px solid #eee4df;
}

.admin-dashboard .admin-course-columns span {
    color: #9a8d87;
    font-size: 10px;
    font-weight: 800;
    line-height: 1.2;
    text-transform: uppercase;
    letter-spacing: .09em;
}

.admin-dashboard .admin-course-list {
    display: flex;
    flex-direction: column;
}

.admin-dashboard .admin-course-item {
    min-height: 82px;
    padding: 15px 4px;
    border-bottom: 1px solid #eee4df;
    transition: background .2s, transform .2s, padding-left .2s;
}

.admin-dashboard .admin-course-item:last-child {
    border-bottom: 0;
}

.admin-dashboard .admin-course-item:hover {
    padding-left: 9px;
    background: linear-gradient(
        90deg,
        rgba(251, 236, 239, .7),
        rgba(255, 255, 255, .2)
    );
    transform: translateX(4px);
}

.admin-dashboard .admin-course-name {
    min-width: 0;
}

.admin-dashboard .admin-course-code {
    display: inline-block;
    margin-bottom: 6px;
    padding: 5px 9px;
    color: #9f1239;
    background: #fbecef;
    border: 1px solid #f0d9de;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: .07em;
}

.admin-dashboard .admin-course-name strong {
    display: block;
    color: #29211e;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 700;
}

.admin-dashboard .admin-course-lecturer {
    min-width: 0;
    color: #514641;
    font-size: 13px;
    line-height: 1.4;
    font-weight: 600;
}

.admin-dashboard .admin-course-sks {
    color: #514641;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 700;
}

.admin-dashboard .admin-course-students {
    color: #514641;
    font-size: 13px;
    line-height: 1.4;
    font-weight: 700;
}

/* Catatan */
.admin-dashboard .admin-dashboard-note {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 22px;
    padding: 15px 18px;
    color: #756863;
    background: #fff;
    border: 1px solid #eadfd9;
    border-radius: 16px;
}

.admin-dashboard .admin-dashboard-note__mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    color: #9f1239;
    background: #fff8f9;
    border: 1px solid #ead9e0;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 800;
}

.admin-dashboard .admin-dashboard-note p {
    margin: 0;
    font-size: 12px;
    line-height: 1.5;
}

/* Responsif tablet */
@media (max-width: 1100px) {
    .admin-dashboard .admin-metrics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .admin-dashboard .admin-course-columns,
    .admin-dashboard .admin-course-item {
        grid-template-columns: minmax(0, 1.5fr) minmax(150px, 1fr) 75px 125px;
        gap: 12px;
    }
}

/* Responsif layar kecil */
@media (max-width: 900px) {
    .admin-dashboard .admin-course-columns {
        display: none;
    }

    .admin-dashboard .admin-course-item {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 17px 4px;
    }
}

@media (max-width: 760px) {
    .admin-dashboard .admin-submission-panel,
        .admin-dashboard .admin-activity-panel,
    .admin-dashboard .admin-quick-panel,
    .admin-dashboard .admin-course-panel,
    .admin-dashboard .admin-task-panel {
        padding: 25px 22px 23px;
    }

    .admin-dashboard .admin-submission-header,
    .admin-dashboard .admin-activity-header,
    .admin-dashboard .admin-course-header,
    .admin-dashboard .admin-task-panel__heading {
        flex-direction: column;
        gap: 18px;
    }

    .admin-dashboard .admin-task-total,
    .admin-dashboard .admin-submission-total,
    .admin-dashboard .admin-course-total {
        min-width: 0;
        min-height: 70px;
        width: 100%;
        text-align: left;
        align-items: flex-start;
    }

    .admin-dashboard .admin-submission-body {
        grid-template-columns: 1fr;
        gap: 22px;
    }

    .admin-dashboard .admin-submission-progress {
        padding-right: 0;
    }

    .admin-dashboard .admin-submission-status {
        border-left: 0;
        border-top: 1px solid #eee4df;
    }

    .admin-dashboard .admin-submission-status__item {
        padding: 0;
    }

    .admin-dashboard .admin-submission-status__item:hover {
        padding-left: 5px;
    }

    .admin-dashboard .admin-activity-item {
        align-items: flex-start;
    }

    .admin-dashboard .admin-activity-time {
        white-space: normal;
        text-align: right;
    }

    .admin-dashboard .admin-quick-actions {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 520px) {
    .admin-dashboard .admin-metrics {
        grid-template-columns: 1fr;
    }
}

/* Komposisi pengguna — khusus Dashboard Admin */
.admin-dashboard .admin-insight-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px;
    margin-top: 22px;
}
.admin-dashboard .admin-insight-panel {
    min-width: 0;
    padding: 28px 30px;
    background: #fff;
    border: 1px solid #eadfd9;
    border-radius: 24px;
    transition: transform .25s, box-shadow .25s, border-color .25s;
}
.admin-dashboard .admin-insight-panel:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 40px rgba(86, 45, 35, .08);
    border-color: #e9c4d0;
}
.admin-dashboard .admin-insight-eyebrow {
    margin: 0 0 7px;
    color: #9f1239;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .16em;
}
.admin-dashboard .admin-insight-title {
    margin: 0;
    color: #172033;
    font-size: 22px;
    line-height: 1.25;
}
.admin-dashboard .admin-insight-description {
    margin: 8px 0 22px;
    color: #7c6f69;
    font-size: 13px;
    line-height: 1.6;
}
.admin-dashboard .admin-role-list {
    display: flex;
    flex-direction: column;
    gap: 13px;
}
.admin-dashboard .admin-role-row {
    padding: 12px 13px;
    background: #fff;
    border: 1px solid #f0e4e5;
    border-radius: 13px;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
}
.admin-dashboard .admin-role-row:hover {
    transform: translateY(-3px);
    background: #fffafb;
    border-color: #e9b8c8;
    box-shadow: 0 8px 18px rgba(159, 18, 57, .09);
}
.admin-dashboard .admin-role-row__top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
    color: #514641;
    font-size: 13px;
}
.admin-dashboard .admin-role-row__top strong {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 31px;
    padding: 6px 10px;
    color: #9f1239;
    background: #fff0f4;
    border: 1px solid #f1d0db;
    border-radius: 9px;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
    transition: background .22s ease, transform .22s ease;
}
.admin-dashboard .admin-role-row:hover .admin-role-row__top strong {
    transform: scale(1.04);
    background: #fce1e9;
}
.admin-dashboard .admin-role-track {
    height: 10px;
    overflow: hidden;
    background: #f0e9e6;
    border-radius: 99px;
}
.admin-dashboard .admin-role-bar {
    height: 100%;
    border-radius: 99px;
    transition: width .7s ease, filter .25s ease;
}
.admin-dashboard .admin-role-row:hover .admin-role-bar {
    filter: saturate(1.3) brightness(1.04);
}
.admin-dashboard .admin-role-bar--admin {
    background: linear-gradient(90deg, #9f1239, #e879a0);
}
.admin-dashboard .admin-role-bar--dosen {
    background: linear-gradient(90deg, #6d28d9, #a78bfa);
}
.admin-dashboard .admin-role-bar--mahasiswa {
    background: linear-gradient(90deg, #2563eb, #60a5fa);
}

.admin-dashboard .admin-role-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-top: 22px;
    padding-top: 17px;
    border-top: 1px solid #eee4df;
}

.admin-dashboard .admin-role-total__label {
    color: #756863;
    font-size: 12px;
    font-weight: 700;
}

.admin-dashboard .admin-role-total__box {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 76px;
    min-height: 58px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #fff1f4 0%, #fce7f3 58%, #f3e8ff 100%);
    border: 1px solid #efcbd9;
    border-radius: 13px;
    box-shadow: 0 3px 10px rgba(159, 18, 57, .035);
    transition: transform .22s ease, box-shadow .22s ease, background .22s ease;
}

.admin-dashboard .admin-role-total__box:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 10px 24px rgba(159, 18, 57, .1);
    background: linear-gradient(135deg, #ffe4ec, #fce7f3 58%, #ede9fe);
}

.admin-dashboard .admin-role-total__box strong {
    color: #9f1239;
    font-size: 25px;
    line-height: 1;
    font-weight: 850;
    letter-spacing: -.035em;
    font-variant-numeric: tabular-nums;
}.admin-dashboard .admin-chart {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    align-items: end;
    gap: 12px;
    min-height: 190px;
    padding: 18px 4px 0;
    border-bottom: 1px solid #eee4df;
}
.admin-dashboard .admin-chart-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    min-width: 0;
    height: 180px;
    cursor: pointer;
    transition: transform .22s ease;
}
.admin-dashboard .admin-chart-column:hover {
    transform: translateY(-5px);
}
.admin-dashboard .admin-chart-value {
    color: #9f1239;
    font-size: 11px;
    font-weight: 800;
    transition: transform .22s ease, color .22s ease;
}
.admin-dashboard .admin-chart-column:hover .admin-chart-value {
    transform: scale(1.15);
    color: #e879a0;
}
.admin-dashboard .admin-chart-track {
    display: flex;
    align-items: flex-end;
    width: min(100%, 34px);
    height: 130px;
    overflow: hidden;
    background: #f8eef1;
    border-radius: 9px 9px 4px 4px;
}
.admin-dashboard .admin-chart-bar {
    width: 100%;
    min-height: 3px;
    background: linear-gradient(180deg, #e879a0, #9f1239);
    border-radius: 8px 8px 3px 3px;
    transition: height .5s ease, filter .2s ease;
}
.admin-dashboard .admin-chart-column:hover .admin-chart-bar {
    filter: saturate(1.4) brightness(1.05);
    box-shadow: 0 0 12px rgba(194, 65, 104, .3);
}
.admin-dashboard .admin-chart-label {
    color: #756863;
    font-size: 10px;
    text-align: center;
    white-space: nowrap;
}
.admin-dashboard .admin-chart-legend {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    color: #958881;
    font-size: 11px;
}
.admin-dashboard .admin-chart-legend::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c24168;
}
@media (max-width: 900px) {
    .admin-dashboard .admin-insight-grid { grid-template-columns: 1fr; }
}
@media (max-width: 520px) {
    .admin-dashboard .admin-insight-panel { padding: 23px 20px; }
    .admin-dashboard .admin-chart { gap: 7px; }
    .admin-dashboard .admin-chart-label { font-size: 9px; }
}

</style>

<section class="admin-dashboard">
    {{-- HEADER --}}
    <header class="admin-dashboard__header">
        <div>
            <p class="admin-dashboard__eyebrow">KAMPUSLMS / ADMIN</p>
            <h1 class="admin-dashboard__title">Dashboard</h1>
            <p class="admin-dashboard__subtitle">
                Pantau data dan aktivitas pembelajaran KampusLMS.
            </p>
        </div>

        <div class="admin-dashboard__date">
            <span class="admin-dashboard__date-dot"></span>
            Sistem aktif
        </div>
    </header>

    {{-- STATISTIK UTAMA --}}
    <section class="admin-metrics" aria-label="Ringkasan KampusLMS">
        <article class="admin-metric admin-metric--pink">
            <div class="admin-metric__top">
                <div class="admin-metric__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M3 21v-2a6 6 0 0 1 12 0v2"></path>
                        <path d="M16 3.5a4 4 0 0 1 0 7"></path>
                        <path d="M18 14a5 5 0 0 1 3 4.6V21"></path>
                    </svg>
                </div>
                <span class="admin-metric__index">01</span>
            </div>

            <div class="admin-metric__number">{{ $totalDosen }}</div>
            <div class="admin-metric__label">Dosen</div>
            <div class="admin-metric__caption">
                Pengajar yang mengelola kegiatan pembelajaran.
            </div>
        </article>

        <article class="admin-metric admin-metric--purple">
            <div class="admin-metric__top">
                <div class="admin-metric__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 10l10-5 10 5-10 5L2 10z"></path>
                        <path d="M6 12.5V17c2.5 2 9.5 2 12 0v-4.5"></path>
                        <path d="M22 10v6"></path>
                    </svg>
                </div>
                <span class="admin-metric__index">02</span>
            </div>

            <div class="admin-metric__number">{{ $totalMahasiswa }}</div>
            <div class="admin-metric__label">Mahasiswa</div>
            <div class="admin-metric__caption">
                Peserta yang mengikuti kegiatan belajar di KampusLMS.
            </div>
        </article>

        <article class="admin-metric admin-metric--blue">
            <div class="admin-metric__top">
                <div class="admin-metric__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path>
                        <path d="M4 5.5v16"></path>
                        <path d="M8 7h8"></path>
                        <path d="M8 11h6"></path>
                    </svg>
                </div>
                <span class="admin-metric__index">03</span>
            </div>

            <div class="admin-metric__number">{{ $totalMataKuliah }}</div>
            <div class="admin-metric__label">Mata Kuliah</div>
            <div class="admin-metric__caption">
                Ruang belajar untuk kegiatan perkuliahan.
            </div>
        </article>

        <article class="admin-metric admin-metric--orange">
            <div class="admin-metric__top">
                <div class="admin-metric__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <path d="M14 2v6h6"></path>
                        <path d="M8 13h8"></path>
                        <path d="M8 17h5"></path>
                    </svg>
                </div>
                <span class="admin-metric__index">04</span>
            </div>

            <div class="admin-metric__number">{{ $totalTugas }}</div>
            <div class="admin-metric__label">Tugas</div>
            <div class="admin-metric__caption">
                Aktivitas yang dikerjakan dan dikumpulkan mahasiswa.
            </div>
        </article>
    </section>

    {{-- KOMPOSISI PENGGUNA DAN GRAFIK PERKEMBANGAN — KHUSUS DASHBOARD ADMIN --}}
    <section class="admin-insight-grid" aria-label="Analisis pengguna dan aktivitas">
        <article class="admin-insight-panel">
            <p class="admin-insight-eyebrow">DATA PENGGUNA</p>
            <h2 class="admin-insight-title">Komposisi Pengguna Berdasarkan Peran</h2>
            <p class="admin-insight-description">
                Perbandingan jumlah akun admin, dosen, dan mahasiswa yang terdaftar di KampusLMS.
            </p>

            <div class="admin-role-list">
                @foreach ($komposisiPengguna as $peran)
                    <div class="admin-role-row">
                        <div class="admin-role-row__top">
                            <span>{{ $peran['label'] }}</span>
                            <strong>{{ $peran['jumlah'] }} akun · {{ $peran['persentase'] }}%</strong>
                        </div>
                        <div class="admin-role-track">
                            <div
                                class="admin-role-bar admin-role-bar--{{ $peran['key'] }}"
                                style="width: {{ $peran['persentase'] }}%;"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
  
            <div class="admin-role-total">
                <div class="admin-role-total__label">Total akun pengguna</div>
                <div class="admin-role-total__box">
                    <strong>{{ $totalPengguna }}</strong>
                </div>
            </div>
        </article>

        <article class="admin-insight-panel">
            <p class="admin-insight-eyebrow">AKTIVITAS PEMBELAJARAN</p>
            <h2 class="admin-insight-title">Perkembangan Pengumpulan</h2>
            <p class="admin-insight-description">
                Jumlah pengumpulan tugas per bulan selama enam bulan terakhir, berdasarkan tanggal pengumpulan.
            
            </p>

            <div class="admin-chart" role="img" aria-label="Grafik jumlah pengumpulan tugas selama enam bulan terakhir">
                @foreach ($grafikPengumpulan as $bulan)
                    <div class="admin-chart-column" title="{{ $bulan['label'] }}: {{ $bulan['jumlah'] }} pengumpulan">
                        <span class="admin-chart-value">{{ $bulan['jumlah'] }}</span>
                        <div class="admin-chart-track">
                            <div
                                class="admin-chart-bar"
                                style="height: {{ $bulan['tinggi'] }}%;"
                            ></div>
                        </div>
                        <span class="admin-chart-label">{{ $bulan['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="admin-chart-legend">Jumlah pengumpulan tugas</div>
        </article>
    </section>

    {{-- TUGAS --}}
    <section class="admin-task-panel">
        <div class="admin-task-panel__heading">
            <div>
                <p class="admin-section-eyebrow">PENGELOLAAN</p>
                <h2 class="admin-section-title">Tugas</h2>
                <p class="admin-section-description">
                    Melihat tugas yang sudah siap dikerjakan dan yang masih disimpan untuk disiapkan.
                </p>
            </div>

            <div class="admin-task-total">
                <span>Jumlah tugas</span>
                <strong>{{ $totalTugas }}</strong>
            </div>
        </div>

        <div class="admin-task-progress">
            <div class="admin-task-progress__top">
                <span>Tugas yang sudah diterbitkan</span>
                <strong>{{ $persentasePublished }}%</strong>
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
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                </div>
                <div>
                    <h3>Tugas diterbitkan</h3>
                    <p>Sudah dapat dilihat dan dikerjakan mahasiswa.</p>
                </div>
            </div>
            <div class="admin-task-row__count">{{ $tugasPublished }}</div>
        </div>

        <div class="admin-task-row admin-task-row--draft">
            <div class="admin-task-row__identity">
                <div class="admin-task-row__icon admin-task-row__icon--draft">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z"></path>
                    </svg>
                </div>
                <div>
                    <h3>Tersimpan sebagai draf</h3>
                    <p>Masih disiapkan dan belum diberikan kepada mahasiswa.</p>
                </div>
            </div>
            <div class="admin-task-row__count">{{ $tugasDraft }}</div>
        </div>
    </section>

    {{-- PEMANTAUAN PENGUMPULAN --}}
    <section class="admin-submission-panel">
        <div class="admin-submission-header">
            <div>
                <p class="admin-submission-eyebrow">PENGUMPULAN TUGAS</p>
                <h2 class="admin-submission-title">Pemantauan Pengumpulan</h2>
                <p class="admin-submission-description">
                    Melihat seberapa banyak tugas yang sudah dikumpulkan mahasiswa dan berapa yang masih menunggu penilaian.
                </p>
            </div>

            <div class="admin-submission-total">
                <span>Jumlah pengumpulan</span>
                <strong>{{ $totalPengumpulan }}</strong>
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
                    Sebanyak {{ $pengumpulanDinilai }} pengumpulan sudah selesai dinilai dari
                    {{ $totalPengumpulan }} pengumpulan.
                </p>
            </div>

            <div class="admin-submission-status">
                <div class="admin-submission-status__item">
                    <div class="admin-submission-status__identity">
                        <div class="admin-submission-status__icon admin-submission-status__icon--done">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                        </div>
                        <span class="admin-submission-status__label">Sudah dinilai</span>
                    </div>
                    <strong class="admin-submission-status__count">
                        {{ $pengumpulanDinilai }}
                    </strong>
                </div>

                <div class="admin-submission-status__item">
                    <div class="admin-submission-status__identity">
                        <div class="admin-submission-status__icon admin-submission-status__icon--pending">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                        </div>
                        <span class="admin-submission-status__label">Menunggu penilaian</span>
                    </div>
                    <strong class="admin-submission-status__count">
                        {{ $pengumpulanMenunggu }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="admin-submission-foot">
            <span class="admin-submission-foot__mark">i</span>
            <span>
                Data dihitung dari pengumpulan yang sudah memiliki nilai dan yang masih menunggu penilaian.
            </span>
        </div>
    </section>

    {{-- AKTIVITAS TERBARU --}}
    <section class="admin-activity-panel">
        <div class="admin-activity-header">
            <div>
                <p class="admin-activity-eyebrow">AKTIVITAS</p>
                <h2 class="admin-activity-title">Aktivitas Terbaru</h2>
                <p class="admin-activity-description">
                    Melihat aktivitas terbaru yang terjadi dalam kegiatan pembelajaran KampusLMS.
                </p>
            </div>
        </div>

        <div class="admin-activity-list">
            @forelse ($aktivitasTerbaru as $aktivitas)
                <div class="admin-activity-item">
                    <div class="admin-activity-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">    
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                            <path d="M8 10h8"></path>
                            <path d="M8 14h5"></path>
                        </svg>
                    </div>

                    <div class="admin-activity-content">
                        <strong>{{ $aktivitas->student?->name ?? 'Mahasiswa' }}</strong>
                        <p>
                            mengumpulkan tugas "{{ $aktivitas->assignment?->title ?? 'Tugas' }}"
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

    {{-- AKSES CEPAT --}}
    <section class="admin-quick-panel">
        <div class="admin-quick-header">
            <p class="admin-quick-eyebrow">AKSES CEPAT</p>
            <h2 class="admin-quick-title">Akses Cepat</h2>
            <p class="admin-quick-description">
                Akses langsung ke bagian administrasi yang paling sering digunakan.
            </p>
        </div>

        <div class="admin-quick-actions">
            <a href="{{ route('admin.users.index') }}" class="admin-quick-action">
                <div class="admin-quick-action__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M19 8v6"></path>
                        <path d="M22 11h-6"></path>
                    </svg>
                </div>
                <div class="admin-quick-action__content">
                    <strong>Kelola Pengguna</strong>
                    <span>Melihat dan mengelola data pengguna KampusLMS.</span>
                </div>
                <span class="admin-quick-action__arrow">→</span>
            </a>

            <a href="{{ route('admin.mata-kuliah.index') }}" class="admin-quick-action">
                <div class="admin-quick-action__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path>
                        <path d="M4 5.5v16"></path>
                        <path d="M8 7h8"></path>
                        <path d="M8 11h6"></path>
                    </svg>
                </div>
                <div class="admin-quick-action__content">
                    <strong>Kelola Mata Kuliah</strong>
                    <span>Melihat dan mengelola daftar mata kuliah KampusLMS.</span>
                </div>
                <span class="admin-quick-action__arrow">→</span>
            </a>
        </div>
    </section>

    {{-- RINGKASAN MATA KULIAH --}}
    <section class="admin-course-panel">
        <div class="admin-course-header">
            <div>
                <p class="admin-course-eyebrow">KATALOG AKADEMIK</p>
                <h2 class="admin-course-title">Ringkasan Mata Kuliah</h2>
                <p class="admin-course-description">
                    Daftar mata kuliah yang tersedia di KampusLMS beserta dosen pengampu,
                    jumlah SKS, dan jumlah mahasiswa yang terdaftar.
                </p>
            </div>

            <div class="admin-course-total">
                <span>Total mata kuliah</span>
                <strong>{{ $totalMataKuliah }}</strong>
            </div>
        </div>

        <div class="admin-course-columns">
            <span>Mata Kuliah</span>
            <span>Dosen Pengampu</span>
            <span>Beban SKS</span>
            <span>Mahasiswa Terdaftar</span>
        </div>

        <div class="admin-course-list">
            @forelse ($ringkasanMataKuliah as $course)
                <div class="admin-course-item">
                    <div class="admin-course-name">
                        <span class="admin-course-code">{{ $course->code }}</span>
                        <strong>{{ $course->name }}</strong>
                    </div>

                    <div class="admin-course-lecturer">
                        {{ $course->lecturer?->name ?? 'Belum ditentukan' }}
                    </div>

                    <div class="admin-course-sks">{{ $course->sks }} SKS</div>
                    <div class="admin-course-students">{{ $course->students_count }} mahasiswa</div>
                </div>
            @empty
                <div class="admin-course-empty">
                    Belum ada mata kuliah yang tersedia.
                </div>
            @endforelse
        </div>
    </section>

    {{-- CATATAN --}}
    <div class="admin-dashboard-note">
        <span class="admin-dashboard-note__mark">i</span>
        <p>Data pada dashboard diperbarui langsung dari data KampusLMS.</p>
    </div>
</section>
</x-layout>