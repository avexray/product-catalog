import { Component, inject, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { JsonPipe } from '@angular/common';

@Component({
  imports: [],
  selector: 'app-import-form',
  styleUrl: './import-form.css',
  templateUrl: './import-form.html',
})
export class ImportForm {
  private readonly http = inject(HttpClient);
  private readonly uploadUrl = 'http://localhost/api/products/import'; // Match your Slim route.

  readonly file = signal<File | null>(null);
  readonly loading = signal(false);
  readonly error = signal('');
  readonly result = signal(false);

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const selected = input.files?.[0] ?? null;

    this.error.set('');
    this.result.set(false);

    if (selected && !selected.name.toLowerCase().endsWith('.xlsx')) {
      this.file.set(null);
      input.value = '';
      this.error.set('Выберите XLSX-файл.');
      return;
    }

    this.file.set(selected);
  }

  upload(event: SubmitEvent): void {
    event.preventDefault();

    const file = this.file();
    if (!file || this.loading()) return;

    const formData = new FormData();
    formData.append('file', file, file.name);

    this.loading.set(true);
    this.error.set('');
    this.result.set(false);

    this.http.post(this.uploadUrl, formData, { observe: 'response' }).subscribe({
      next: (response) => {
        this.result.set(response.status === 202);
      },
      error: (err: HttpErrorResponse) => {
        this.error.set(
          err.error?.error ??
          err.error?.message ??
          `Ошибка загрузки файла ${err.status ? ` (HTTP ${err.status})` : ''}.`
        );
        this.loading.set(false);
      },
      complete: () => this.loading.set(false),
    });
  }
}
