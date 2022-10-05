import { Component, OnInit, Inject } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';

export interface ImageClass {
  imagen?: string;
  
}

@Component({
  selector: 'app-view-imagen',
  templateUrl: './view-imagen.component.html',
  styleUrls: ['./view-imagen.component.css']
})

export class ViewImagenComponent implements OnInit {

  imagen:any;
  constructor(
    @Inject(MAT_DIALOG_DATA) public data: ImageClass,
    public dialog: MatDialog,
    public dialogRef: MatDialogRef<ViewImagenComponent>,
    private _sanitizer: DomSanitizer) { }

  ngOnInit(): void {
    console.log(this.data.imagen);
    this.imagen = this._sanitizer.bypassSecurityTrustResourceUrl("data:image/png;base64,"+this.data.imagen);
  }

}
