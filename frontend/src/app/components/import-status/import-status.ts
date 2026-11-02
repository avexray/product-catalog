import { Component, DestroyRef, inject, signal } from '@angular/core';
import { ImportStatusService } from '../../services/import-status/import-status.service';
import { DatePipe } from '@angular/common';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { ImportJob } from '../../models/import-job';

@Component({
  imports: [DatePipe],
  selector: 'app-import-status',
  styleUrl: './import-status.css',
  templateUrl: './import-status.html',
})
export class ImportStatus {
  private readonly importStatusService = inject(ImportStatusService);
  private readonly destroyRef = inject(DestroyRef);

  readonly job = signal<ImportJob | null>(null);
  readonly error = signal<string | null>(null);
  readonly none = signal(false);

  ngOnInit(): void {
    this.importStatusService
      .pollStatus()
      .pipe(takeUntilDestroyed(this.destroyRef))
      .subscribe((response) => {
        if (!response) {
          this.error.set('Could not load import status. Retrying…');
          return;
        }

        if (response.data == null) {
          this.job.set(null);
          this.none.set(true);
          this.error.set(null);
          return;
        }

        this.job.set(response.data);
        this.none.set(false);
        this.error.set(null);
      });
  }

  get progressPercent(): number {
    const job = this.job();

    if (!job || job.totalRows === 0) {
      return 0;
    }

    return Math.min(
      100,
      Math.round((job.processedRows / job.totalRows) * 100),
    );
  }

  get statusBadgeClass(): string {
    switch (this.job()?.status) {
      case 'completed':
        return 'bg-success';
      case 'completed_with_errors':
        return 'bg-warning text-dark';
      case 'failed':
        return 'bg-danger';
      case 'processing':
        return 'bg-primary';
      default:
        return 'bg-secondary';
    }
  }

  get progressBarClass(): string {
    return this.job()?.status === 'failed' ? 'bg-danger' : '';
  }
}
