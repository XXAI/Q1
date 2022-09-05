import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VictimasDialogComponent } from './victimas-dialog.component';

describe('VictimasDialogComponent', () => {
  let component: VictimasDialogComponent;
  let fixture: ComponentFixture<VictimasDialogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ VictimasDialogComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(VictimasDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
