import { inject, Service } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { from, Observable, switchMap, timer } from 'rxjs';
import { ImportJob } from '../../models/import-job';

export interface ImportStatusResponse {
  statusCode: number;
  data?: ImportJob | null;
}

@Service()
export class ImportStatusService {
  private readonly http = inject(HttpClient);
  private readonly url = 'http://localhost/api/products/status';

  getLatestStatus(): Observable<ImportStatusResponse> {
    return this.http.get<ImportStatusResponse>(this.url);
  }

  pollStatus(): Observable<ImportStatusResponse> {
    return timer(0, 5_000).pipe(
      switchMap(() => this.getLatestStatus()),
    );
  }
}
